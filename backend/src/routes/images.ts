/**
 * Routes pour la génération d'images de personnages
 */
import express from 'express'
import { generateCharacterImageAuto } from '../services/imageGenerationService'
import { supabase } from '../config/supabase'

const router = express.Router()

/**
 * POST /api/images/generate
 * Génère une image pour un personnage
 * Body: { id_personnage, element, niveau, nom, isEnemy?, force? }
 * force: si true, régénère l'image même si elle existe déjà
 */
router.post('/generate', async (req, res) => {
  try {
    const { id_personnage, element, niveau, nom, isEnemy, force } = req.body

    if (!id_personnage || !element || !niveau || !nom) {
      return res.status(400).json({ 
        error: 'Paramètres manquants: id_personnage, element, niveau, nom requis' 
      })
    }

    // Vérifier si une image existe déjà dans la base de données (sauf si force=true)
    if (!force && supabase) {
      const { data: existingPersonnage } = await supabase
        .from('personnage')
        .select('image_url')
        .eq('id_personnage', id_personnage)
        .single()

      // Si une image existe déjà et est valide, la retourner
      if (existingPersonnage?.image_url) {
        return res.json({ 
          image_url: existingPersonnage.image_url,
          cached: true 
        })
      }
    }

    // Générer une nouvelle image
    console.log(`🔄 Génération d'image pour le personnage ${id_personnage}: ${nom}`)
    const imageUrl = await generateCharacterImageAuto({
      nom,
      element,
      niveau,
      isEnemy: isEnemy || false,
    })

    if (!imageUrl) {
      const hasOpenAI = !!(process.env.OPENAI_API_KEY || process.env.OPEN_API_KEY)
      const hasReplicate = !!process.env.REPLICATE_API_TOKEN
      
      let errorMessage = 'Impossible de générer l\'image. '
      if (!hasOpenAI && !hasReplicate) {
        errorMessage += 'Aucune clé API configurée. Ajoutez OPENAI_API_KEY (ou OPEN_API_KEY) ou REPLICATE_API_TOKEN dans backend/.env'
      } else if (hasOpenAI) {
        errorMessage += 'Erreur avec OpenAI DALL-E. Vérifiez votre clé API et vos crédits.'
      } else {
        errorMessage += 'Erreur avec Replicate. Vérifiez votre token API.'
      }
      
      return res.status(500).json({ 
        error: errorMessage,
        configured: {
          openai: hasOpenAI,
          replicate: hasReplicate
        }
      })
    }

    // Sauvegarder l'URL de l'image dans la base de données
    if (supabase) {
      await supabase
        .from('personnage')
        .update({ image_url: imageUrl })
        .eq('id_personnage', id_personnage)
    }

    res.json({ 
      image_url: imageUrl,
      cached: false 
    })
  } catch (error) {
    console.error('Erreur lors de la génération d\'image:', error)
    res.status(500).json({ 
      error: 'Erreur lors de la génération d\'image',
      details: error instanceof Error ? error.message : 'Unknown error'
    })
  }
})

/**
 * POST /api/images/regenerate/:id
 * Force la régénération d'une image pour un personnage
 */
router.post('/regenerate/:id', async (req, res) => {
  try {
    const { id } = req.params

    if (!supabase) {
      return res.status(500).json({ error: 'Configuration Supabase manquante' })
    }

    // Récupérer les informations du personnage
    const { data: personnage, error } = await supabase
      .from('personnage')
      .select('id_personnage, nom, element, niveau, image_url, id_equipe')
      .eq('id_personnage', id)
      .single()

    if (error || !personnage) {
      return res.status(404).json({ error: 'Personnage non trouvé' })
    }

    console.log(`🔄 Régénération forcée de l'image pour: ${personnage.nom}`)

    // Générer une nouvelle image (forcer la génération)
    const imageUrl = await generateCharacterImageAuto({
      nom: personnage.nom,
      element: personnage.element || 1,
      niveau: personnage.niveau || 1,
      isEnemy: false,
    })

    if (!imageUrl) {
      const hasOpenAI = !!(process.env.OPENAI_API_KEY || process.env.OPEN_API_KEY)
      const hasReplicate = !!process.env.REPLICATE_API_TOKEN
      
      // Retourner 503 (Service Unavailable) au lieu de 500 si pas de clé API
      const statusCode = (!hasOpenAI && !hasReplicate) ? 503 : 500
      
      let errorMessage = 'Impossible de générer l\'image. '
      if (!hasOpenAI && !hasReplicate) {
        errorMessage += 'Aucune clé API configurée. Ajoutez OPENAI_API_KEY (ou OPEN_API_KEY) ou REPLICATE_API_TOKEN dans backend/.env'
      } else if (hasOpenAI) {
        errorMessage += 'Erreur avec OpenAI DALL-E. Vérifiez votre clé API et vos crédits.'
      } else {
        errorMessage += 'Erreur avec Replicate. Vérifiez votre token API.'
      }
      
      return res.status(statusCode).json({ 
        error: errorMessage,
        configured: {
          openai: hasOpenAI,
          replicate: hasReplicate
        }
      })
    }

    // Sauvegarder la nouvelle URL
    if (supabase) {
      await supabase
        .from('personnage')
        .update({ image_url: imageUrl })
        .eq('id_personnage', id)
    }

    res.json({ 
      image_url: imageUrl,
      cached: false,
      regenerated: true
    })
  } catch (error) {
    console.error('Erreur lors de la régénération d\'image:', error)
    res.status(500).json({ 
      error: 'Erreur lors de la régénération d\'image',
      details: error instanceof Error ? error.message : 'Unknown error'
    })
  }
})

/**
 * GET /api/images/character/:id
 * Récupère l'URL de l'image d'un personnage (génère si nécessaire)
 */
router.get('/character/:id', async (req, res) => {
  try {
    const { id } = req.params

    if (!supabase) {
      return res.status(500).json({ error: 'Configuration Supabase manquante' })
    }

    // Récupérer les informations du personnage
    const { data: personnage, error } = await supabase
      .from('personnage')
      .select('id_personnage, nom, element, niveau, image_url, id_equipe')
      .eq('id_personnage', id)
      .single()

    if (error || !personnage) {
      return res.status(404).json({ error: 'Personnage non trouvé' })
    }

    // Si une image existe déjà, la retourner
    if (personnage.image_url) {
      return res.json({ 
        image_url: personnage.image_url,
        cached: true 
      })
    }

    // Sinon, générer une nouvelle image
    const imageUrl = await generateCharacterImageAuto({
      nom: personnage.nom,
      element: personnage.element || 1,
      niveau: personnage.niveau || 1,
      isEnemy: false,
    })

    if (!imageUrl) {
      const hasOpenAI = !!(process.env.OPENAI_API_KEY || process.env.OPEN_API_KEY)
      const hasReplicate = !!process.env.REPLICATE_API_TOKEN
      
      // Retourner 503 (Service Unavailable) au lieu de 500 si pas de clé API
      const statusCode = (!hasOpenAI && !hasReplicate) ? 503 : 500
      
      let errorMessage = 'Impossible de générer l\'image. '
      if (!hasOpenAI && !hasReplicate) {
        errorMessage += 'Aucune clé API configurée. Ajoutez OPENAI_API_KEY (ou OPEN_API_KEY) ou REPLICATE_API_TOKEN dans backend/.env'
      } else if (hasOpenAI) {
        errorMessage += 'Erreur avec OpenAI DALL-E. Vérifiez votre clé API et vos crédits.'
      } else {
        errorMessage += 'Erreur avec Replicate. Vérifiez votre token API.'
      }
      
      return res.status(statusCode).json({ 
        error: errorMessage,
        configured: {
          openai: hasOpenAI,
          replicate: hasReplicate
        }
      })
    }

    // Sauvegarder l'URL
    if (supabase) {
      await supabase
        .from('personnage')
        .update({ image_url: imageUrl })
        .eq('id_personnage', id)
    }

    res.json({ 
      image_url: imageUrl,
      cached: false 
    })
  } catch (error) {
    console.error('Erreur lors de la récupération/génération d\'image:', error)
    res.status(500).json({ 
      error: 'Erreur lors de la récupération/génération d\'image',
      details: error instanceof Error ? error.message : 'Unknown error'
    })
  }
})

export default router

