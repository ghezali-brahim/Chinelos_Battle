import { supabase } from '../config/supabase'
import { generateEnemyTeam, findWeakestAlivePersonnage } from './enemyService'
import {
  calculateDamage,
  applyDefense,
  getElementMultiplier,
  applyDamage,
  consumeMP,
  isTeamDead,
  parseAttaques,
} from '../utils/combatUtils'
import { addExperience } from './personnageService'
import { Personnage, Element, Attaque } from '../../types/models'

export interface CombatState {
  id_combat?: number
  joueur_team: Personnage[]
  enemy_team: Personnage[]
  current_turn: 'joueur' | 'enemy'
  current_personnage_index_joueur: number
  current_personnage_index_enemy: number
  nombre_tour: number
  finit: boolean
  winner?: 'joueur' | 'enemy'
}

export interface AttackResult {
  attacker: Personnage
  target: Personnage
  damage: number
  elementMultiplier: number
  targetHpAfter: number
  success: boolean
  error?: string
}

export interface CombatRewards {
  argent: number
  xpPoints: number // Points d'XP absolus au lieu de pourcentage
  xpGained: number
}

/**
 * Initialise un nouveau combat
 */
export async function initializeCombat(
  userId: string,
  enemyLevel: number | null = null
): Promise<CombatState> {
  // Récupérer l'équipe active du joueur
  const { data: equipes } = await supabase!
    .from('equipe')
    .select('id_equipe')
    .eq('id_user', userId)
    .order('id_equipe', { ascending: true })
    .limit(2)

  if (!equipes || equipes.length === 0) {
    throw new Error('Aucune équipe trouvée. Veuillez d\'abord créer une équipe.')
  }

  const id_equipe_active = equipes[0].id_equipe

  // Récupérer les personnages de l'équipe active
  const { data: joueurPersonnages } = await supabase!
    .from('personnage')
    .select('*')
    .eq('id_equipe', id_equipe_active)
    .order('niveau', { ascending: false })

  if (!joueurPersonnages || joueurPersonnages.length === 0) {
    throw new Error('Votre équipe active est vide. Achetez des personnages à la boutique!')
  }

  // Filtrer les personnages morts
  const alivePersonnages = joueurPersonnages.filter((p: Personnage) => p.hp > 0)

  if (alivePersonnages.length === 0) {
    throw new Error('Votre équipe active n\'a plus de personnages vivants. Allez à la boutique pour les soigner!')
  }

  // Calculer le niveau total du joueur
  const niveauTotalJoueur = alivePersonnages.reduce((sum, p) => sum + p.niveau, 0)

  // Générer l'équipe ennemie
  const enemyTeamData = await generateEnemyTeam(enemyLevel || niveauTotalJoueur)

  // Créer le combat en base de données
  const { data: combat, error } = await supabase!
    .from('combats')
    .insert({
      id_joueur_1: userId,
      id_joueur_2: null, // IA
      nombre_tour: 1,
      finit: false,
    })
    .select()
    .single()

  if (error) {
    throw new Error(`Erreur création combat: ${error.message}`)
  }

  return {
    id_combat: combat.id_combat,
    joueur_team: alivePersonnages as Personnage[],
    enemy_team: enemyTeamData.personnages,
    current_turn: 'joueur',
    current_personnage_index_joueur: 0,
    current_personnage_index_enemy: 0,
    nombre_tour: 1,
    finit: false,
  }
}

/**
 * Exécute une attaque du joueur
 */
export async function executePlayerAttack(
  combatState: CombatState,
  attaqueIndex: number,
  targetEnemyIndex: number,
  elements: Element[],
  attaques: Attaque[]
): Promise<{ combatState: CombatState; attackResult: AttackResult }> {
  const attacker = combatState.joueur_team[combatState.current_personnage_index_joueur]
  const target = combatState.enemy_team[targetEnemyIndex]

  if (!attacker || !target) {
    throw new Error('Personnage attaquant ou cible invalide')
  }

  if (attacker.hp <= 0) {
    throw new Error('Le personnage attaquant est mort')
  }

  if (target.hp <= 0) {
    throw new Error('La cible est déjà morte')
  }

  try {
    // Calculer les dégâts
    const baseDamage = calculateDamage(attacker, attaqueIndex, attaques)

    // Multiplicateur d'élément
    const elementMultiplier = getElementMultiplier(attacker.element, target.element, elements)
    const damageBeforeDefense = baseDamage * elementMultiplier

    // Appliquer la défense
    const finalDamage = applyDefense(damageBeforeDefense, target.defense)

    // Appliquer les dégâts
    const updatedTarget = applyDamage(target, finalDamage)

    // Consommer les MP
    const attaquesIds = parseAttaques(attacker.attaques)
    const attaqueId = attaquesIds[attaqueIndex]
    const attaque = attaques.find(a => a.id_attaque === attaqueId)

    if (!attaque) {
      throw new Error('Attaque non trouvée')
    }

    const updatedAttacker = consumeMP(attacker, attaque.mp_used)

    // Mettre à jour l'état du combat
    const updatedEnemyTeam = [...combatState.enemy_team]
    updatedEnemyTeam[targetEnemyIndex] = updatedTarget

    const updatedJoueurTeam = [...combatState.joueur_team]
    updatedJoueurTeam[combatState.current_personnage_index_joueur] = updatedAttacker

    // Passer au personnage suivant du joueur
    let nextJoueurIndex = (combatState.current_personnage_index_joueur + 1) % updatedJoueurTeam.length
    // Trouver le prochain personnage vivant
    let attempts = 0
    const maxAttempts = updatedJoueurTeam.length * 2 // Double check pour être sûr
    while (attempts < maxAttempts && updatedJoueurTeam[nextJoueurIndex] && updatedJoueurTeam[nextJoueurIndex].hp <= 0) {
      nextJoueurIndex = (nextJoueurIndex + 1) % updatedJoueurTeam.length
      attempts++
    }
    
    // Si aucun personnage vivant trouvé, garder l'index actuel (le combat se terminera)
    if (attempts >= maxAttempts || !updatedJoueurTeam[nextJoueurIndex] || updatedJoueurTeam[nextJoueurIndex].hp <= 0) {
      // Trouver le premier personnage vivant dans toute l'équipe
      const aliveIndex = updatedJoueurTeam.findIndex(p => p.hp > 0)
      if (aliveIndex >= 0) {
        nextJoueurIndex = aliveIndex
      }
    }

    const newCombatState: CombatState = {
      ...combatState,
      joueur_team: updatedJoueurTeam,
      enemy_team: updatedEnemyTeam,
      current_personnage_index_joueur: nextJoueurIndex,
    }

    // Vérifier si l'équipe ennemie est morte
    if (isTeamDead(updatedEnemyTeam)) {
      newCombatState.finit = true
      newCombatState.winner = 'joueur'
    } else if (isTeamDead(updatedJoueurTeam)) {
      newCombatState.finit = true
      newCombatState.winner = 'enemy'
    }

    const attackResult: AttackResult = {
      attacker: updatedAttacker,
      target: updatedTarget,
      damage: finalDamage,
      elementMultiplier,
      targetHpAfter: updatedTarget.hp,
      success: true,
    }

    return { combatState: newCombatState, attackResult }
  } catch (error: any) {
    return {
      combatState,
      attackResult: {
        attacker,
        target,
        damage: 0,
        elementMultiplier: 1.0,
        targetHpAfter: target.hp,
        success: false,
        error: error.message,
      },
    }
  }
}

/**
 * Exécute le tour de l'IA
 */
export async function executeAITurn(
  combatState: CombatState,
  elements: Element[],
  attaques: Attaque[]
): Promise<{ combatState: CombatState; attackResult: AttackResult | null }> {
  const attacker = combatState.enemy_team[combatState.current_personnage_index_enemy]

  if (!attacker || attacker.hp <= 0) {
    // Passer au personnage suivant de l'IA
    let nextIndex = (combatState.current_personnage_index_enemy + 1) % combatState.enemy_team.length
    let attempts = 0
    while (combatState.enemy_team[nextIndex].hp <= 0 && attempts < combatState.enemy_team.length) {
      nextIndex = (nextIndex + 1) % combatState.enemy_team.length
      attempts++
    }

    return {
      combatState: {
        ...combatState,
        current_personnage_index_enemy: nextIndex,
      },
      attackResult: null,
    }
  }

  // L'IA cible toujours le personnage le plus faible
  const target = findWeakestAlivePersonnage(combatState.joueur_team)

  if (!target) {
    // Plus de cibles vivantes, le joueur a perdu
    return {
      combatState: {
        ...combatState,
        finit: true,
        winner: 'enemy',
      },
      attackResult: null,
    }
  }

  const targetIndex = combatState.joueur_team.findIndex(p => p.id_personnage === target.id_personnage)

  try {
    // L'IA utilise l'attaque 0 (la première), ou 2 si pas assez de MP
    let attaqueIndex = 0
    const attaquesIds = parseAttaques(attacker.attaques)
    const attaque = attaques.find(a => a.id_attaque === attaquesIds[0])

    if (!attaque || attacker.mp < attaque.mp_used) {
      attaqueIndex = 2 // Utiliser l'attaque 3 qui coûte 0 MP
    }

    // Calculer les dégâts
    const baseDamage = calculateDamage(attacker, attaqueIndex, attaques)

    // Multiplicateur d'élément
    const elementMultiplier = getElementMultiplier(attacker.element, target.element, elements)
    const damageBeforeDefense = baseDamage * elementMultiplier

    // Appliquer la défense
    const finalDamage = applyDefense(damageBeforeDefense, target.defense)

    // Appliquer les dégâts
    const updatedTarget = applyDamage(target, finalDamage)

    // Consommer les MP
    const attaqueUsed = attaques.find(a => a.id_attaque === attaquesIds[attaqueIndex])
    const updatedAttacker = attaqueUsed ? consumeMP(attacker, attaqueUsed.mp_used) : attacker

    // Mettre à jour l'état du combat
    const updatedJoueurTeam = [...combatState.joueur_team]
    updatedJoueurTeam[targetIndex] = updatedTarget

    const updatedEnemyTeam = [...combatState.enemy_team]
    updatedEnemyTeam[combatState.current_personnage_index_enemy] = updatedAttacker

    // Passer au personnage suivant de l'IA
    let nextEnemyIndex = (combatState.current_personnage_index_enemy + 1) % updatedEnemyTeam.length
    let attempts = 0
    const maxAttempts = updatedEnemyTeam.length * 2
    while (attempts < maxAttempts && updatedEnemyTeam[nextEnemyIndex] && updatedEnemyTeam[nextEnemyIndex].hp <= 0) {
      nextEnemyIndex = (nextEnemyIndex + 1) % updatedEnemyTeam.length
      attempts++
    }
    
    // Si aucun personnage vivant trouvé, trouver le premier vivant
    if (attempts >= maxAttempts || !updatedEnemyTeam[nextEnemyIndex] || updatedEnemyTeam[nextEnemyIndex].hp <= 0) {
      const aliveIndex = updatedEnemyTeam.findIndex(p => p.hp > 0)
      if (aliveIndex >= 0) {
        nextEnemyIndex = aliveIndex
      }
    }

    const newCombatState: CombatState = {
      ...combatState,
      joueur_team: updatedJoueurTeam,
      enemy_team: updatedEnemyTeam,
      current_personnage_index_enemy: nextEnemyIndex,
      nombre_tour: combatState.nombre_tour + 1,
    }

    // Vérifier si une équipe est morte
    if (isTeamDead(updatedJoueurTeam)) {
      newCombatState.finit = true
      newCombatState.winner = 'enemy'
    } else if (isTeamDead(updatedEnemyTeam)) {
      newCombatState.finit = true
      newCombatState.winner = 'joueur'
    }

    const attackResult: AttackResult = {
      attacker: updatedAttacker,
      target: updatedTarget,
      damage: finalDamage,
      elementMultiplier,
      targetHpAfter: updatedTarget.hp,
      success: true,
    }

    return { combatState: newCombatState, attackResult }
  } catch (error: any) {
    // En cas d'erreur, passer au personnage suivant
    let nextIndex = (combatState.current_personnage_index_enemy + 1) % combatState.enemy_team.length
    return {
      combatState: {
        ...combatState,
        current_personnage_index_enemy: nextIndex,
        nombre_tour: combatState.nombre_tour + 1,
      },
      attackResult: null,
    }
  }
}

/**
 * Calcule les récompenses après un combat gagné
 * Utilise un système de points d'XP fixes basé sur un ratio standard multiplié par les niveaux des ennemis
 */
export function calculateRewards(
  joueurLevelTotal: number,
  enemyLevelTotal: number,
  enemyTeamSize: number = 1
): CombatRewards {
  // Ratio standard : 10 XP de base par niveau ennemi
  const XP_RATIO_PER_ENEMY_LEVEL = 10
  
  // Calculer le niveau moyen des ennemis
  const enemyAverageLevel = enemyTeamSize > 0 ? enemyLevelTotal / enemyTeamSize : 1

  // XP de base = ratio × niveau total des ennemis
  let baseXP = enemyLevelTotal * XP_RATIO_PER_ENEMY_LEVEL

  // Bonus pour le nombre d'ennemis (encourage les combats multiples)
  // Bonus de 20% par ennemi supplémentaire (au-delà de 1)
  const enemyCountMultiplier = 1 + (Math.max(0, enemyTeamSize - 1) * 0.2)
  baseXP = Math.round(baseXP * enemyCountMultiplier)

  // Bonus de difficulté selon le rapport de force
  let difficultyMultiplier = 1.0
  
  if (joueurLevelTotal < enemyLevelTotal) {
    // Si l'ennemi est plus fort, bonus de 50%
    difficultyMultiplier = 1.5
  } else if (joueurLevelTotal > enemyLevelTotal * 1.5) {
    // Si le joueur est beaucoup plus fort, réduction de 30%
    difficultyMultiplier = 0.7
  } else if (joueurLevelTotal > enemyLevelTotal * 1.2) {
    // Si le joueur est un peu plus fort, réduction de 15%
    difficultyMultiplier = 0.85
  }

  // Calcul final de l'XP
  const finalXP = Math.max(5, Math.round(baseXP * difficultyMultiplier))

  // Argent basé sur le niveau moyen et le nombre d'ennemis
  let baseArgent = Math.round(enemyAverageLevel * 2)
  const argentBonusFromCount = Math.max(0, enemyTeamSize - 1) * 1
  let finalArgent = baseArgent + argentBonusFromCount

  // Bonus d'argent pour difficulté
  if (joueurLevelTotal < enemyLevelTotal) {
    finalArgent = Math.round(finalArgent * 1.5)
  } else if (joueurLevelTotal > enemyLevelTotal * 1.5) {
    finalArgent = Math.round(finalArgent * 0.7)
  }

  finalArgent = Math.max(3, finalArgent)

  return {
    argent: finalArgent,
    xpPoints: finalXP, // Points d'XP absolus
    xpGained: 0, // Sera calculé lors de l'application
  }
}

/**
 * Applique les récompenses au joueur
 */
export async function applyRewards(
  userId: string,
  combatState: CombatState,
  rewards: CombatRewards
): Promise<{ newBalance: number; xpAdded: number }> {
  // Ajouter l'argent
  const { data: user } = await supabase!
    .from('users')
    .select('argent, nombre_victoire')
    .eq('id_user', userId)
    .single()

  if (!user) {
    throw new Error('Utilisateur non trouvé')
  }

  const newBalance = user.argent + rewards.argent
  const newVictoires = (user.nombre_victoire || 0) + 1

  await supabase!
    .from('users')
    .update({
      argent: newBalance,
      nombre_victoire: newVictoires,
    })
    .eq('id_user', userId)

  // Ajouter l'XP à tous les personnages de l'équipe active
  let totalXpAdded = 0
  for (const personnage of combatState.joueur_team) {
    if (personnage.hp > 0) {
      // Note: addExperience nécessite un appel à la BD pour chaque personnage
      // Pour l'instant, on retourne juste l'info
      totalXpAdded += rewards.xpPoints
    }
  }

  return {
    newBalance,
    xpAdded: totalXpAdded,
  }
}

/**
 * Enregistre l'état du combat dans la base de données
 */
export async function saveCombatState(combatState: CombatState): Promise<void> {
  if (!combatState.id_combat) return

  // Mettre à jour les personnages du joueur
  for (const personnage of combatState.joueur_team) {
    await supabase!
      .from('personnage')
      .update({
        hp: personnage.hp,
        mp: personnage.mp,
      })
      .eq('id_personnage', personnage.id_personnage)
  }

  // Mettre à jour le combat
  await supabase!
    .from('combats')
    .update({
      nombre_tour: combatState.nombre_tour,
      finit: combatState.finit,
      indice_perso_j1: combatState.current_personnage_index_joueur,
      indice_perso_j2: combatState.current_personnage_index_enemy,
    })
    .eq('id_combat', combatState.id_combat)
}

