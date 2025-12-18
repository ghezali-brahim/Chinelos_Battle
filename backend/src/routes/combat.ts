import { Router } from 'express'
import { authenticate, AuthRequest } from '../middleware/auth'
import { supabase } from '../config/supabase'
import {
  initializeCombat,
  executePlayerAttack,
  executeAITurn,
  calculateRewards,
  applyRewards,
  saveCombatState,
  CombatState,
} from '../services/combatService'
import { generateEnemyList } from '../services/enemyService'
import { addExperience } from '../services/personnageService'
import { Element, Attaque } from '../types/models'

const router = Router()

// Vérifier Supabase
router.use((req, res, next) => {
  if (!supabase) {
    return res.status(500).json({ error: 'Supabase not configured' })
  }
  next()
})

// Toutes les routes nécessitent une authentification
router.use(authenticate)

// Variable temporaire pour stocker les états de combat en mémoire
// En production, utiliser Redis ou une base de données
const combatStates = new Map<number, CombatState>()

// Récupérer les données statiques (éléments et attaques)
async function getStaticData() {
  const [elementsResult, attaquesResult] = await Promise.all([
    supabase!.from('element').select('*').order('id_element'),
    supabase!.from('attaque').select('*').order('id_attaque'),
  ])

  return {
    elements: (elementsResult.data || []) as Element[],
    attaques: (attaquesResult.data || []) as Attaque[],
  }
}

// Créer un combat
router.post('/create', async (req: AuthRequest, res) => {
  try {
    const { enemyLevel } = req.body // Niveau de l'ennemi (optionnel)
    const userId = req.user!.id

    const combatState = await initializeCombat(userId, enemyLevel || null)
    combatStates.set(combatState.id_combat!, combatState)

    res.json({
      id_combat: combatState.id_combat,
      joueur_team: combatState.joueur_team,
      enemy_team: combatState.enemy_team,
      nombre_tour: combatState.nombre_tour,
    })
  } catch (error: any) {
    res.status(400).json({ error: error.message })
  }
})

// Obtenir l'état d'un combat
router.get('/:id', async (req: AuthRequest, res) => {
  try {
    const { id } = req.params
    const combatId = parseInt(id)
    const userId = req.user!.id

    // Vérifier que le combat appartient au joueur
    const { data: combat } = await supabase!
      .from('combats')
      .select('id_joueur_1')
      .eq('id_combat', combatId)
      .single()

    if (!combat || combat.id_joueur_1 !== userId) {
      return res.status(403).json({ error: 'Unauthorized' })
    }

    let combatState = combatStates.get(combatId)

    // Si le combat n'est pas en mémoire, le recréer depuis la BD
    if (!combatState) {
      // Essayer de récupérer depuis la BD et reconstruire l'état
      // Pour l'instant, retourner une erreur car on ne peut pas recréer l'état complet
      return res.status(404).json({ 
        error: 'Combat not found in memory. Please start a new combat.',
        combat_id: combatId 
      })
    }

    // Fonction pour trouver le prochain personnage vivant
    const findNextAlivePersonnage = (team: any[], startIndex: number): number => {
      let currentIndex = startIndex
      let attempts = 0
      
      // Vérifier si le personnage actuel est vivant
      if (team[currentIndex] && team[currentIndex].hp > 0) {
        return currentIndex
      }
      
      // Chercher le prochain personnage vivant
      while (attempts < team.length) {
        currentIndex = (currentIndex + 1) % team.length
        if (team[currentIndex] && team[currentIndex].hp > 0) {
          return currentIndex
        }
        attempts++
      }
      
      // Si aucun personnage vivant, chercher le premier disponible
      const aliveIndex = team.findIndex(p => p.hp > 0)
      return aliveIndex >= 0 ? aliveIndex : startIndex
    }

    // Vérifier et corriger l'index du personnage actuel si nécessaire
    if (combatState.current_turn === 'joueur' && !combatState.finit) {
      const currentPersonnage = combatState.joueur_team[combatState.current_personnage_index_joueur]
      if (!currentPersonnage || currentPersonnage.hp <= 0) {
        // Le personnage actuel est mort, trouver le suivant
        const nextIndex = findNextAlivePersonnage(
          combatState.joueur_team,
          combatState.current_personnage_index_joueur
        )
        combatState.current_personnage_index_joueur = nextIndex
        combatStates.set(combatId, combatState)
        
        // Si aucun personnage vivant, terminer le combat
        const alivePersonnage = combatState.joueur_team.find(p => p.hp > 0)
        if (!alivePersonnage) {
          combatState.finit = true
          combatState.winner = 'enemy'
          combatStates.set(combatId, combatState)
        }
      }
    }

    // Même chose pour l'ennemi
    if (combatState.current_turn === 'enemy' && !combatState.finit) {
      const currentEnemy = combatState.enemy_team[combatState.current_personnage_index_enemy]
      if (!currentEnemy || currentEnemy.hp <= 0) {
        const nextIndex = findNextAlivePersonnage(
          combatState.enemy_team,
          combatState.current_personnage_index_enemy
        )
        combatState.current_personnage_index_enemy = nextIndex
        combatStates.set(combatId, combatState)
        
        const aliveEnemy = combatState.enemy_team.find(p => p.hp > 0)
        if (!aliveEnemy) {
          combatState.finit = true
          combatState.winner = 'joueur'
          combatStates.set(combatId, combatState)
        }
      }
    }

    res.json({
      id_combat: combatState.id_combat,
      joueur_team: combatState.joueur_team,
      enemy_team: combatState.enemy_team,
      current_turn: combatState.current_turn,
      current_personnage_index_joueur: combatState.current_personnage_index_joueur,
      current_personnage_index_enemy: combatState.current_personnage_index_enemy,
      nombre_tour: combatState.nombre_tour,
      finit: combatState.finit,
      winner: combatState.winner,
    })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Exécuter une attaque du joueur
router.post('/:id/attack', async (req: AuthRequest, res) => {
  try {
    const { id } = req.params
    const { indice_attaque, indice_perso_enemy } = req.body
    const combatId = parseInt(id)
    const userId = req.user!.id

    let combatState = combatStates.get(combatId)

    // Si le combat n'est pas en mémoire, essayer de le récupérer depuis la BD
    if (!combatState) {
      // Pour l'instant, on retourne une erreur claire
      return res.status(404).json({ 
        error: 'Combat not found in memory. The combat may have been restarted.',
        combat_id: combatId 
      })
    }

    if (combatState.finit) {
      return res.status(400).json({ error: 'Le combat est terminé' })
    }

    // Vérifier que c'est le tour du joueur
    if (combatState.current_turn !== 'joueur') {
      return res.status(400).json({ error: "Ce n'est pas votre tour" })
    }

    const { elements, attaques } = await getStaticData()

    // Exécuter l'attaque
    const { combatState: updatedState, attackResult } = await executePlayerAttack(
      combatState,
      indice_attaque,
      indice_perso_enemy,
      elements,
      attaques
    )

    // Sauvegarder l'état
    combatStates.set(combatId, updatedState)
    await saveCombatState(updatedState)

    // Si le combat n'est pas terminé, exécuter le tour de l'IA
    let aiAttackResult = null
    if (!updatedState.finit) {
      const { combatState: afterAI, attackResult: aiResult } = await executeAITurn(
        updatedState,
        elements,
        attaques
      )
      combatStates.set(combatId, afterAI)
      await saveCombatState(afterAI)
      aiAttackResult = aiResult
    }

    // Si le combat est terminé et le joueur a gagné, appliquer les récompenses
    let rewards = null
    if (updatedState.finit && updatedState.winner === 'joueur') {
      const niveauTotalJoueur = updatedState.joueur_team.reduce((sum, p) => sum + p.niveau, 0)
      const niveauTotalEnemy = updatedState.enemy_team.reduce((sum, p) => sum + p.niveau, 0)
      const enemyTeamSize = updatedState.enemy_team.length
      const calculatedRewards = calculateRewards(niveauTotalJoueur, niveauTotalEnemy, enemyTeamSize)
      
      // Appliquer les récompenses
      const { newBalance } = await applyRewards(userId, updatedState, calculatedRewards)
      
      // Ajouter l'XP à tous les personnages
      for (const personnage of updatedState.joueur_team) {
        if (personnage.hp > 0) {
          await addExperience(personnage, calculatedRewards.xpPoints)
        }
      }

      rewards = {
        ...calculatedRewards,
        newBalance,
      }
    } else if (updatedState.finit && updatedState.winner === 'enemy') {
      // Incrémenter les défaites
      const { data: user } = await supabase!
        .from('users')
        .select('nombre_defaite')
        .eq('id_user', userId)
        .single()

      if (user) {
        await supabase!
          .from('users')
          .update({ nombre_defaite: (user.nombre_defaite || 0) + 1 })
          .eq('id_user', userId)
      }
    }

    res.json({
      combatState: updatedState,
      attackResult,
      aiAttackResult,
      rewards,
    })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Exécuter le tour de l'IA (séparé si nécessaire)
router.post('/:id/ai-turn', async (req: AuthRequest, res) => {
  try {
    const { id } = req.params
    const combatId = parseInt(id)

    const combatState = combatStates.get(combatId)
    if (!combatState) {
      return res.status(404).json({ error: 'Combat not found' })
    }

    const { elements, attaques } = await getStaticData()
    const { combatState: updatedState, attackResult } = await executeAITurn(
      combatState,
      elements,
      attaques
    )

    combatStates.set(combatId, updatedState)
    await saveCombatState(updatedState)

    res.json({
      combatState: updatedState,
      attackResult,
    })
  } catch (error: any) {
    res.status(500).json({ error: error.message })
  }
})

// Obtenir la liste des ennemis disponibles
router.get('/enemies/list', async (req: AuthRequest, res) => {
  try {
    const userId = req.user!.id

    // Récupérer l'équipe active du joueur pour calculer le niveau total
    const { data: equipes, error: equipesError } = await supabase!
      .from('equipe')
      .select('id_equipe')
      .eq('id_user', userId)
      .order('id_equipe', { ascending: true })
      .limit(1)

    if (equipesError) {
      console.error('Erreur récupération équipes:', equipesError)
      return res.status(400).json({ error: equipesError.message })
    }

    // Si pas d'équipe, créer les équipes
    if (!equipes || equipes.length === 0) {
      const { data: newEquipes, error: createError } = await supabase!
        .from('equipe')
        .insert([{ id_user: userId }, { id_user: userId }])
        .select('id_equipe')
        .order('id_equipe', { ascending: true })

      if (createError || !newEquipes || newEquipes.length === 0) {
        return res.status(400).json({ error: 'Impossible de créer les équipes' })
      }

      // Nouveau joueur sans personnages, générer des ennemis de base (niveau 1-10)
      const enemies = await generateEnemyList(5) // Niveau de base pour les nouveaux joueurs

      return res.json(
        enemies.map((enemy, index) => ({
          index: index - 2,
          niveauTotal: enemy.niveauTotal,
          nombrePersonnages: enemy.personnages.length,
        }))
      )
    }

    // Récupérer les personnages de l'équipe active
    const { data: personnages, error: personnagesError } = await supabase!
      .from('personnage')
      .select('niveau')
      .eq('id_equipe', equipes[0].id_equipe)

    if (personnagesError) {
      console.error('Erreur récupération personnages:', personnagesError)
      return res.status(400).json({ error: personnagesError.message })
    }

    // Calculer le niveau total (minimum 1 pour éviter les erreurs)
    let niveauTotal = 1
    if (personnages && personnages.length > 0) {
      niveauTotal = personnages.reduce((sum, p) => sum + (p.niveau || 0), 0)
    }

    if (niveauTotal < 1) {
      niveauTotal = 1
    }

    // Générer la liste d'ennemis (niveau -2 à +7)
    const enemies = await generateEnemyList(niveauTotal)

    if (!enemies || enemies.length === 0) {
      // Fallback: générer des ennemis de base
      const fallbackEnemies = await generateEnemyList(5)
      return res.json(
        fallbackEnemies.map((enemy, index) => ({
          index: index - 2,
          niveauTotal: enemy.niveauTotal,
          nombrePersonnages: enemy.personnages.length,
        }))
      )
    }

    res.json(
      enemies.map((enemy, index) => ({
        index: index - 2, // Offset pour correspondre au système original (-2 à +7)
        niveauTotal: enemy.niveauTotal,
        nombrePersonnages: enemy.personnages.length,
      }))
    )
  } catch (error: any) {
    console.error('Erreur génération liste ennemis:', error)
    res.status(500).json({ error: error.message || 'Erreur lors de la génération des ennemis' })
  }
})

export default router

