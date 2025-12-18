import { Router } from 'express'

const router = Router()

// Envoyer un message de contact
router.post('/', async (req, res) => {
  try {
    const { nom, email, message } = req.body

    if (!nom || !email || !message) {
      return res.status(400).json({ error: 'Missing required fields' })
    }

    // TODO: Implémenter l'envoi d'email via Supabase Edge Functions ou service externe
    // Pour l'instant, juste une confirmation
    console.log('Contact form submission:', { nom, email, message })

    res.json({ message: 'Message envoyé avec succès' })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

export default router

