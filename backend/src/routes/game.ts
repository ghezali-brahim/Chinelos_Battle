import { Router } from 'express'
import { supabase } from '../config/supabase'

const router = Router()

// Vérifier Supabase
router.use((req, res, next) => {
  if (!supabase) {
    return res.status(500).json({ error: 'Supabase not configured' })
  }
  next()
})

// Obtenir tous les éléments
router.get('/element', async (req, res) => {
  try {
    const { data, error } = await supabase!
      .from('element')
      .select('*')
      .order('id_element')

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    res.json(data || [])
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Obtenir toutes les attaques
router.get('/attaque', async (req, res) => {
  try {
    const { data, error } = await supabase!
      .from('attaque')
      .select('*')
      .order('id_attaque')

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    res.json(data || [])
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Obtenir tous les niveaux (pour calculer XP)
router.get('/niveau', async (req, res) => {
  try {
    const { data, error } = await supabase!
      .from('niveau')
      .select('*')
      .order('niveau')

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    res.json(data || [])
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

export default router

