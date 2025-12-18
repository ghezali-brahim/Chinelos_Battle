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

// Obtenir le profil du joueur
router.get('/profile', async (req: AuthRequest, res) => {
  try {
    const userId = req.user!.id

    const { data, error } = await supabase!
      .from('users')
      .select('*')
      .eq('id_user', userId)
      .single()

    if (error) {
      return res.status(404).json({ error: 'User not found' })
    }

    res.json(data)
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Obtenir les équipes du joueur
router.get('/equipes', async (req: AuthRequest, res) => {
  try {
    const userId = req.user!.id

    const { data: equipes, error: equipesError } = await supabase!
      .from('equipe')
      .select('*')
      .eq('id_user', userId)
      .order('id_equipe', { ascending: true })

    if (equipesError) {
      return res.status(400).json({ error: equipesError.message })
    }

    if (!equipes || equipes.length === 0) {
      // Créer 2 équipes si elles n'existent pas
      const { data: newEquipe1 } = await supabase!
        .from('equipe')
        .insert({ id_user: userId })
        .select()
        .single()

      const { data: newEquipe2 } = await supabase!
        .from('equipe')
        .insert({ id_user: userId })
        .select()
        .single()

      return res.json([
        { ...newEquipe1, personnages: [] },
        { ...newEquipe2, personnages: [] },
      ])
    }

    // Pour chaque équipe, récupérer les personnages
    const equipesAvecPersonnages = await Promise.all(
      equipes.map(async (equipe) => {
        const { data: personnages } = await supabase!
          .from('personnage')
          .select('*')
          .eq('id_equipe', equipe.id_equipe)
          .order('niveau', { ascending: false })

        return {
          ...equipe,
          personnages: personnages || [],
        }
      })
    )

    res.json(equipesAvecPersonnages)
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Obtenir l'équipe active
router.get('/equipe/active', async (req: AuthRequest, res) => {
  try {
    const userId = req.user!.id

    const { data: equipes } = await supabase!
      .from('equipe')
      .select('id_equipe')
      .eq('id_user', userId)
      .order('id_equipe', { ascending: true })
      .limit(1)

    if (!equipes || equipes.length === 0) {
      return res.json({ id_equipe: null, personnages: [] })
    }

    const { data: personnages } = await supabase!
      .from('personnage')
      .select('*')
      .eq('id_equipe', equipes[0].id_equipe)
      .order('niveau', { ascending: false })

    res.json({
      id_equipe: equipes[0].id_equipe,
      personnages: personnages || [],
    })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Obtenir l'équipe de réserve
router.get('/equipe/reserve', async (req: AuthRequest, res) => {
  try {
    const userId = req.user!.id

    const { data: equipes } = await supabase!
      .from('equipe')
      .select('id_equipe')
      .eq('id_user', userId)
      .order('id_equipe', { ascending: true })
      .limit(2)

    if (!equipes || equipes.length < 2) {
      return res.json({ id_equipe: null, personnages: [] })
    }

    const { data: personnages } = await supabase!
      .from('personnage')
      .select('*')
      .eq('id_equipe', equipes[1].id_equipe)
      .order('niveau', { ascending: false })

    res.json({
      id_equipe: equipes[1].id_equipe,
      personnages: personnages || [],
    })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Transférer un personnage entre équipes
router.post('/equipe/transfer', async (req: AuthRequest, res) => {
  try {
    const { id_personnage, toActive } = req.body
    const userId = req.user!.id

    if (!id_personnage || toActive === undefined) {
      return res.status(400).json({ error: 'id_personnage et toActive requis' })
    }

    // Récupérer les équipes
    const { data: equipes } = await supabase!
      .from('equipe')
      .select('id_equipe')
      .eq('id_user', userId)
      .order('id_equipe', { ascending: true })
      .limit(2)

    if (!equipes || equipes.length < 2) {
      return res.status(400).json({ error: 'Équipes non trouvées' })
    }

    const id_equipe_active = equipes[0].id_equipe
    const id_equipe_reserve = equipes[1].id_equipe

    // Vérifier que le personnage appartient au joueur
    const { data: personnage } = await supabase!
      .from('personnage')
      .select('id_equipe')
      .eq('id_personnage', id_personnage)
      .in('id_equipe', [id_equipe_active, id_equipe_reserve])
      .single()

    if (!personnage) {
      return res.status(404).json({ error: 'Personnage non trouvé ou n\'appartient pas au joueur' })
    }

    const targetEquipe = toActive ? id_equipe_active : id_equipe_reserve

    // Vérifier la limite de 6 personnages pour l'équipe active
    if (toActive) {
      const { data: activePersonnages } = await supabase!
        .from('personnage')
        .select('id_personnage')
        .eq('id_equipe', id_equipe_active)

      if (activePersonnages && activePersonnages.length >= 6) {
        return res.status(400).json({ error: 'L\'équipe active est complète (max 6 personnages)' })
      }
    }

    // Transférer
    const { error: updateError } = await supabase!
      .from('personnage')
      .update({ id_equipe: targetEquipe })
      .eq('id_personnage', id_personnage)

    if (updateError) {
      return res.status(400).json({ error: updateError.message })
    }

    res.json({ message: 'Personnage transféré avec succès' })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Modifier une équipe
router.put('/equipe/:id', async (req: AuthRequest, res) => {
  try {
    const { id } = req.params
    const userId = req.user!.id
    const updates = req.body

    // Vérifier que l'équipe appartient au joueur
    const { data: equipe } = await supabase!
      .from('equipe')
      .select('id_user')
      .eq('id_equipe', id)
      .single()

    if (!equipe || equipe.id_user !== userId) {
      return res.status(403).json({ error: 'Unauthorized' })
    }

    // TODO: Implémenter la modification d'équipe
    res.json({ message: 'Team updated', team_id: id })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Obtenir le classement
router.get('/leaderboard', async (req: AuthRequest, res) => {
  try {
    const { data: users, error } = await supabase!
      .from('users')
      .select('id_user, username, nombre_victoire, nombre_defaite, connected, last_connection')
      .not('last_connection', 'is', null)
      .order('nombre_victoire', { ascending: false })
      .limit(100)

    if (error) {
      return res.status(400).json({ error: error.message })
    }

    if (!users) {
      return res.json([])
    }

    // Calculer le niveau total pour chaque joueur
    const leaderboardPromises = users.map(async (user, index) => {
      // Récupérer l'équipe active et calculer le niveau total
      const { data: equipes } = await supabase!
        .from('equipe')
        .select('id_equipe')
        .eq('id_user', user.id_user)
        .order('id_equipe', { ascending: true })
        .limit(1)

      let niveauTotal = 0
      if (equipes && equipes.length > 0) {
        const { data: personnages } = await supabase!
          .from('personnage')
          .select('niveau')
          .eq('id_equipe', equipes[0].id_equipe)

        niveauTotal = personnages?.reduce((sum, p) => sum + (p.niveau || 0), 0) || 0
      }

      return {
        id_user: user.id_user,
        username: user.username,
        nombre_victoire: user.nombre_victoire || 0,
        nombre_defaite: user.nombre_defaite || 0,
        connected: user.connected || false,
        rang: index + 1,
        niveauTotal,
      }
    })

    const leaderboard = await Promise.all(leaderboardPromises)

    // Trier par niveau total puis par victoires
    leaderboard.sort((a, b) => {
      if (b.niveauTotal !== a.niveauTotal) {
        return b.niveauTotal - a.niveauTotal
      }
      return (b.nombre_victoire || 0) - (a.nombre_victoire || 0)
    })

    // Réassigner les rangs après tri
    leaderboard.forEach((item, index) => {
      item.rang = index + 1
    })

    res.json(leaderboard)
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Obtenir les statistiques complètes du joueur
router.get('/stats', async (req: AuthRequest, res) => {
  try {
    const userId = req.user!.id

    const { data: user } = await supabase!
      .from('users')
      .select('*')
      .eq('id_user', userId)
      .single()

    if (!user) {
      return res.status(404).json({ error: 'Utilisateur non trouvé' })
    }

    // Récupérer les équipes
    const { data: equipes } = await supabase!
      .from('equipe')
      .select('id_equipe')
      .eq('id_user', userId)
      .order('id_equipe', { ascending: true })
      .limit(2)

    let niveauTotal = 0
    let nombrePersonnages = 0
    let niveauMax = 0

    if (equipes && equipes.length > 0) {
      const { data: personnages } = await supabase!
        .from('personnage')
        .select('niveau')
        .eq('id_equipe', equipes[0].id_equipe)

      if (personnages) {
        niveauTotal = personnages.reduce((sum, p) => sum + (p.niveau || 0), 0)
        nombrePersonnages = personnages.length
        niveauMax = Math.max(...personnages.map(p => p.niveau || 0), 0)
      }
    }

    res.json({
      ...user,
      niveauTotal,
      nombrePersonnages,
      niveauMax,
    })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

export default router

