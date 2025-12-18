import { supabase } from '../config/supabase'
import { Personnage } from '../types/models'

const CARAC_ADD_FOR_UPPING = {
  hp_max: 10,
  mp_max: 5,
  puissance: 3,
  defense: 1,
}

interface EnemyTeam {
  personnages: Personnage[]
  niveauTotal: number
}

/**
 * Génère une équipe d'ennemis IA basée sur le niveau total du joueur
 */
export async function generateEnemyTeam(niveauTotal: number): Promise<EnemyTeam> {
  // Assurer un niveau minimum de 1
  if (niveauTotal <= 0) niveauTotal = 1

  const personnages: Personnage[] = []
  let sommeNiveau = 0
  const maxPersonnages = Math.min(6, Math.max(1, Math.ceil(niveauTotal / 3))) // Entre 1 et 6 personnages

  // Générer des personnages jusqu'à atteindre ou dépasser le niveau total
  let i = 0
  while (sommeNiveau < niveauTotal && personnages.length < maxPersonnages) {
    i++
    
    // Niveau aléatoire entre 1/8 et 1/2 du niveau total (minimum 1)
    const minNiveau = Math.max(1, Math.floor(niveauTotal / 8))
    const maxNiveau = Math.max(minNiveau, Math.floor(niveauTotal / 2))
    let niveauAleatoire = Math.floor(Math.random() * (maxNiveau - minNiveau + 1)) + minNiveau
    
    if (niveauAleatoire < 1) niveauAleatoire = 1

    // Pour le dernier personnage ou si on approche du niveau total
    const resteNiveau = niveauTotal - sommeNiveau
    if (resteNiveau > 0 && resteNiveau < niveauAleatoire && personnages.length < maxPersonnages) {
      niveauAleatoire = resteNiveau
    }

    // Ne pas dépasser le niveau total de trop
    if (sommeNiveau + niveauAleatoire > niveauTotal * 1.2) {
      niveauAleatoire = Math.max(1, niveauTotal - sommeNiveau)
    }

    try {
      const personnage = await createEnemyPersonnage(niveauAleatoire, `Monster ${i}`)
      personnages.push(personnage)
      sommeNiveau += niveauAleatoire

      // Si on a atteint le niveau cible, on peut s'arrêter
      if (sommeNiveau >= niveauTotal && personnages.length >= 1) {
        break
      }
    } catch (error) {
      console.error(`Erreur création personnage ennemi niveau ${niveauAleatoire}:`, error)
      // Continue avec le prochain personnage
    }
  }

  // Assurer au moins un personnage
  if (personnages.length === 0) {
    try {
      const fallbackPersonnage = await createEnemyPersonnage(1, 'Monster 1')
      personnages.push(fallbackPersonnage)
      sommeNiveau = 1
    } catch (error) {
      console.error('Erreur création personnage fallback:', error)
      // Créer un personnage minimal si tout échoue
      const minimalPersonnage: Personnage = {
        id_personnage: Math.floor(Math.random() * 1000000) + 9000,
        nom: 'Monster 1',
        element: 1,
        niveau: 1,
        experience: 0,
        attaques: '1;2;3',
        hp: 10,
        hp_max: 10,
        mp: 5,
        mp_max: 5,
        puissance: 3,
        defense: 1,
        id_equipe: null,
      }
      personnages.push(minimalPersonnage)
      sommeNiveau = 1
    }
  }

  return {
    personnages,
    niveauTotal: Math.max(sommeNiveau, 1),
  }
}

/**
 * Crée un personnage ennemi avec les stats appropriées pour son niveau
 */
async function createEnemyPersonnage(niveau: number, nom: string): Promise<Personnage> {
  // Assurer un niveau minimum de 1
  if (niveau < 1) niveau = 1

  // Récupérer l'XP nécessaire pour ce niveau
  let experience = 0
  try {
    const { data: niveauData, error } = await supabase!
      .from('niveau')
      .select('experience')
      .eq('niveau', niveau)
      .single()

    if (error || !niveauData) {
      // Si le niveau n'existe pas, calculer une XP approximative
      // Formule basique: 10 * niveau * (niveau - 1) / 2 (série arithmétique)
      experience = Math.max(0, 10 * niveau * (niveau - 1) / 2)
    } else {
      experience = niveauData.experience || 0
    }
  } catch (error) {
    // En cas d'erreur, utiliser une formule de fallback
    experience = Math.max(0, 10 * niveau * (niveau - 1) / 2)
  }

  // Calculer les stats
  const hp_max = CARAC_ADD_FOR_UPPING.hp_max * niveau
  const mp_max = CARAC_ADD_FOR_UPPING.mp_max * niveau
  const puissance = CARAC_ADD_FOR_UPPING.puissance * niveau
  const defense = CARAC_ADD_FOR_UPPING.defense * niveau

  // Élément aléatoire (1-4)
  const element = Math.floor(Math.random() * 4) + 1

  // Attaques par défaut : "1;2;3"
  const attaques = '1;2;3'

  const personnage: Personnage = {
    id_personnage: Math.floor(Math.random() * 1000000) + 9000, // ID temporaire pour IA
    nom,
    element,
    niveau,
    experience,
    attaques,
    hp: hp_max,
    hp_max,
    mp: mp_max,
    mp_max,
    puissance,
    defense,
    id_equipe: null, // Pas d'équipe pour les ennemis IA (pas sauvegardé en BD)
  }

  return personnage
}

/**
 * Génère une liste d'ennemis de différents niveaux (-2 à +7 du niveau du joueur)
 */
export async function generateEnemyList(niveauTotalJoueur: number): Promise<EnemyTeam[]> {
  const enemies: EnemyTeam[] = []
  
  // Assurer un niveau minimum de 1
  const baseNiveau = Math.max(1, niveauTotalJoueur)
  
  for (let i = -2; i < 8; i++) {
    const niveauEnemy = baseNiveau + i
    if (niveauEnemy >= 1) {
      try {
        const enemyTeam = await generateEnemyTeam(niveauEnemy)
        if (enemyTeam && enemyTeam.personnages.length > 0) {
          enemies.push(enemyTeam)
        }
      } catch (error) {
        console.error(`Erreur génération ennemi niveau ${niveauEnemy}:`, error)
        // Continue avec les autres ennemis
      }
    }
  }

  // Si aucun ennemi n'a été généré, créer au moins un ennemi de base
  if (enemies.length === 0) {
    try {
      const fallbackEnemy = await generateEnemyTeam(3)
      enemies.push(fallbackEnemy)
    } catch (error) {
      console.error('Erreur génération ennemi fallback:', error)
    }
  }

  return enemies
}

/**
 * Trouve le personnage le plus faible vivant d'une équipe (pour l'IA)
 */
export function findWeakestAlivePersonnage(personnages: Personnage[]): Personnage | null {
  const alive = personnages.filter(p => p.hp > 0)
  if (alive.length === 0) return null

  // Trouver celui avec le moins de HP
  return alive.reduce((weakest, current) => {
    if (current.hp < weakest.hp) return current
    if (current.hp === weakest.hp && current.niveau < weakest.niveau) return current
    return weakest
  })
}

