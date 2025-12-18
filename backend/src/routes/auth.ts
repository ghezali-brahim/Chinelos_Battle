import { Router } from 'express'
import { supabasePublic, supabase } from '../config/supabase'

const router = Router()

// Login
router.post('/login', async (req, res) => {
  try {
    if (!supabasePublic) {
      return res.status(500).json({ error: 'Supabase not configured' })
    }

    const { email, password } = req.body

    if (!email || !password) {
      return res.status(400).json({ error: 'Email and password required' })
    }

    const { data, error } = await supabasePublic.auth.signInWithPassword({
      email,
      password,
    })

    if (error) {
      return res.status(401).json({ error: error.message })
    }

    res.json({
      user: data.user,
      session: data.session,
    })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Register
router.post('/register', async (req, res) => {
  try {
    if (!supabasePublic) {
      return res.status(500).json({ error: 'Supabase not configured' })
    }

    const { email, password, username } = req.body

    if (!email || !password || !username) {
      return res.status(400).json({ error: 'Email, password and username required' })
    }

    // Validation
    if (!/^[a-zA-Z0-9]{4,}$/.test(username)) {
      return res.status(400).json({
        error: 'Username must be at least 4 alphanumeric characters'
      })
    }

    const { data, error } = await supabasePublic.auth.signUp({
      email,
      password,
      options: {
        data: {
          username,
        },
      },
    })

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    // Créer l'utilisateur dans la table users et les équipes
    if (data.user && supabase) {
      try {
        // Créer l'utilisateur dans la table users
        const { error: userError } = await supabase.from('users').insert({
          id_user: data.user.id,
          username,
          email,
          argent: 10, // Argent de départ
          nombre_victoire: 0,
          nombre_defaite: 0,
          connected: true,
          last_connection: new Date().toISOString(),
        })

        if (userError) {
          console.error('Erreur création utilisateur:', userError)
        }

        // Créer les 2 équipes (active et réserve)
        const { error: equipeError } = await supabase.from('equipe').insert([
          { id_user: data.user.id },
          { id_user: data.user.id },
        ])

        if (equipeError) {
          console.error('Erreur création équipes:', equipeError)
        }
      } catch (err: any) {
        console.error('Erreur lors de la création du profil:', err)
        // Continue même en cas d'erreur, l'utilisateur peut toujours se connecter
      }
    }

    res.json({
      user: data.user,
      session: data.session,
    })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Logout
router.post('/logout', async (req, res) => {
  try {
    if (!supabasePublic) {
      return res.status(500).json({ error: 'Supabase not configured' })
    }

    const { error } = await supabasePublic.auth.signOut()
    if (error) {
      return res.status(500).json({ error: error.message })
    }
    res.json({ message: 'Logged out successfully' })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

export default router

