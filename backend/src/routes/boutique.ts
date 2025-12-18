import { Router } from 'express'
import { authenticate, AuthRequest } from '../middleware/auth'
import { supabase } from '../config/supabase'
import { buyPersonnage, buyHeal } from '../services/boutiqueService'

const router = Router()

router.use(authenticate)

// Vérifier Supabase
router.use((req, res, next) => {
  if (!supabase) {
    return res.status(500).json({ error: 'Supabase not configured' })
  }
  next()
})

// Obtenir la liste des items
router.get('/items', async (req: AuthRequest, res) => {
  try {
    const { data, error } = await supabase!
      .from('item')
      .select('*')
      .order('id_item')

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    res.json(data || [])
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Acheter un personnage
router.post('/buy-personnage', async (req: AuthRequest, res) => {
  try {
    const { nom_personnage, id_element } = req.body
    const userId = req.user!.id

    if (!nom_personnage || !id_element) {
      return res.status(400).json({ error: 'nom_personnage et id_element requis' })
    }

    // Récupérer l'équipe de réserve (équipe 2)
    const { data: equipes } = await supabase!
      .from('equipe')
      .select('id_equipe')
      .eq('id_user', userId)
      .order('id_equipe', { ascending: true })
      .limit(2)

    if (!equipes || equipes.length < 2) {
      return res.status(400).json({ error: 'Équipes non trouvées. Veuillez créer votre profil.' })
    }

    const id_equipe_reserve = equipes[1].id_equipe

    const result = await buyPersonnage(userId, nom_personnage, id_element, id_equipe_reserve)

    res.json({
      message: 'Personnage acheté avec succès',
      personnage: result.personnage,
      newBalance: result.newBalance,
    })
  } catch (error: any) {
    res.status(400).json({ error: error.message })
  }
})

// Soigner l'équipe
router.post('/buy-soin', async (req: AuthRequest, res) => {
  try {
    const userId = req.user!.id

    // Récupérer l'équipe active (équipe 1)
    const { data: equipes } = await supabase!
      .from('equipe')
      .select('id_equipe')
      .eq('id_user', userId)
      .order('id_equipe', { ascending: true })
      .limit(1)

    if (!equipes || equipes.length === 0) {
      return res.status(400).json({ error: 'Équipe non trouvée' })
    }

    const id_equipe_active = equipes[0].id_equipe

    const result = await buyHeal(userId, id_equipe_active)

    res.json({
      message: 'Équipe soignée avec succès',
      newBalance: result.newBalance,
      healedCount: result.healedCount,
    })
  } catch (error: any) {
    res.status(400).json({ error: error.message })
  }
})

// Acheter un item (pour compatibilité avec l'ancien code)
router.post('/buy', async (req: AuthRequest, res) => {
  try {
    const { type, nom_personnage, id_element } = req.body
    const userId = req.user!.id

    if (type === 'personnage') {
      if (!nom_personnage || !id_element) {
        return res.status(400).json({ error: 'nom_personnage et id_element requis' })
      }

      const { data: equipes } = await supabase!
        .from('equipe')
        .select('id_equipe')
        .eq('id_user', userId)
        .order('id_equipe', { ascending: true })
        .limit(2)

      if (!equipes || equipes.length < 2) {
        return res.status(400).json({ error: 'Équipes non trouvées' })
      }

      const result = await buyPersonnage(userId, nom_personnage, id_element, equipes[1].id_equipe)
      return res.json({ message: 'Personnage acheté', ...result })
    } else if (type === 'soin') {
      const { data: equipes } = await supabase!
        .from('equipe')
        .select('id_equipe')
        .eq('id_user', userId)
        .order('id_equipe', { ascending: true })
        .limit(1)

      if (!equipes || equipes.length === 0) {
        return res.status(400).json({ error: 'Équipe non trouvée' })
      }

      const result = await buyHeal(userId, equipes[0].id_equipe)
      return res.json({ message: 'Équipe soignée', ...result })
    } else {
      return res.status(400).json({ error: 'Type d\'achat invalide. Types: personnage, soin' })
    }
  } catch (error: any) {
    res.status(400).json({ error: error.message })
  }
})

// Obtenir l'inventaire du joueur
router.get('/inventory', async (req: AuthRequest, res) => {
  try {
    const userId = req.user!.id

    const { data, error } = await supabase!
      .from('inventaire')
      .select(`
        *,
        item (*)
      `)
      .eq('id_user', userId)

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    res.json(data || [])
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

export default router

