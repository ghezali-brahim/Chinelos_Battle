import { Router } from 'express'
import { authenticate, AuthRequest } from '../middleware/auth'
import { supabase } from '../config/supabase'

const router = Router()

router.use(authenticate)

// Vérifier Supabase
router.use((req, res, next) => {
  if (!supabase) {
    return res.status(500).json({ error: 'Supabase not configured' })
  }
  next()
})

// Obtenir les messages (envoyés et reçus)
router.get('/messages', async (req: AuthRequest, res) => {
  try {
    const userId = req.user!.id

    const { data, error } = await supabase!
      .from('messages')
      .select('*')
      .or(`id_expeditaire.eq.${userId},id_destinataire.eq.${userId}`)
      .order('date_envoie', { ascending: false })

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    // Séparer les messages envoyés et reçus
    const messageEnvoyes = data.filter(m => m.id_expeditaire === userId)
    const messageRecus = data.filter(m => m.id_destinataire === userId)

    res.json({
      envoyes: messageEnvoyes,
      recus: messageRecus,
    })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Envoyer un message
router.post('/send', async (req: AuthRequest, res) => {
  try {
    const { objet, contenu, id_destinataire } = req.body
    const id_expeditaire = req.user!.id

    if (!objet || !contenu || !id_destinataire) {
      return res.status(400).json({ error: 'Missing required fields' })
    }

    const { data, error } = await supabase!
      .from('messages')
      .insert({
        objet,
        contenu,
        id_expeditaire,
        id_destinataire,
      })
      .select()
      .single()

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    res.json(data)
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Marquer un message comme lu
router.put('/:id/read', async (req: AuthRequest, res) => {
  try {
    const { id } = req.params
    const userId = req.user!.id

    // Vérifier que le message est destiné à cet utilisateur
    const { data: message } = await supabase!
      .from('messages')
      .select('id_destinataire')
      .eq('id_message', id)
      .single()

    if (!message || message.id_destinataire !== userId) {
      return res.status(403).json({ error: 'Unauthorized' })
    }

    const { data, error } = await supabase!
      .from('messages')
      .update({ lu: true })
      .eq('id_message', id)
      .select()
      .single()

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    res.json(data)
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

export default router

