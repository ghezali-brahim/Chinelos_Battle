import { supabase } from '../config/supabase'
import { Personnage } from '../types/models'

const CARAC_ADD_FOR_UPPING = {
  hp_max: 10,
  mp_max: 5,
  puissance: 3,
  defense: 1,
}

/**
 * Crée un personnage avec les stats appropriées pour son niveau
 */
export async function createPersonnage(
  niveau: number,
  nom: string,
  id_element: number | null = null,
  id_equipe: number | null = null
): Promise<Personnage> {
  // Récupérer l'XP nécessaire pour ce niveau
  const { data: niveauData } = await supabase!
    .from('niveau')
    .select('experience')
    .eq('niveau', niveau)
    .single()

  if (!niveauData) {
    throw new Error(`Niveau ${niveau} introuvable dans la base de données`)
  }

  const experience = niveauData.experience

  // Élément aléatoire si non spécifié
  if (!id_element) {
    id_element = Math.floor(Math.random() * 4) + 1
  }

  // Calculer les stats
  const hp_max = CARAC_ADD_FOR_UPPING.hp_max * niveau
  const mp_max = CARAC_ADD_FOR_UPPING.mp_max * niveau
  const puissance = CARAC_ADD_FOR_UPPING.puissance * niveau
  const defense = CARAC_ADD_FOR_UPPING.defense * niveau

  // Attaques par défaut : "1;2;3"
  const attaques = '1;2;3'

  // Insérer dans la base de données
  const { data: inserted, error } = await supabase!
    .from('personnage')
    .insert({
      nom,
      element: id_element,
      niveau,
      experience,
      attaques,
      hp: hp_max,
      hp_max,
      mp: mp_max,
      mp_max,
      puissance,
      defense,
      id_equipe,
    })
    .select()
    .single()

  if (error) {
    throw new Error(`Erreur création personnage: ${error.message}`)
  }

  return inserted as Personnage
}

/**
 * Calcule le niveau d'un personnage à partir de son XP
 */
export async function getLevelFromXP(experience: number): Promise<number> {
  const { data } = await supabase!
    .from('niveau')
    .select('niveau, experience')
    .lte('experience', experience)
    .order('niveau', { ascending: false })
    .limit(1)
    .single()

  return data?.niveau || 1
}

/**
 * Ajoute de l'expérience à un personnage et gère la montée de niveau
 */
export async function addExperience(
  personnage: Personnage,
  xpToAdd: number
): Promise<{ personnage: Personnage; leveledUp: boolean; newLevel?: number }> {
  const newExperience = personnage.experience + xpToAdd
  const currentLevel = personnage.niveau

  // Récupérer tous les niveaux pour calculer le nouveau niveau
  const { data: niveaux } = await supabase!
    .from('niveau')
    .select('*')
    .order('niveau', { ascending: true })

  if (!niveaux) {
    throw new Error('Impossible de récupérer les niveaux')
  }

  // Calculer le nouveau niveau
  let newLevel = currentLevel
  for (const niveau of niveaux) {
    if (newExperience >= niveau.experience) {
      newLevel = niveau.niveau
    } else {
      break
    }
  }

  let updatedPersonnage: Personnage = {
    ...personnage,
    experience: newExperience,
  }

  let leveledUp = false

  // Si le niveau a augmenté, mettre à jour les stats
  if (newLevel > currentLevel) {
    leveledUp = true
    const levelsGained = newLevel - currentLevel

    // Augmenter les stats pour chaque niveau gagné
    const newHpMax = personnage.hp_max + CARAC_ADD_FOR_UPPING.hp_max * levelsGained
    const newMpMax = personnage.mp_max + CARAC_ADD_FOR_UPPING.mp_max * levelsGained
    const newPuissance = personnage.puissance + CARAC_ADD_FOR_UPPING.puissance * levelsGained
    const newDefense = personnage.defense + CARAC_ADD_FOR_UPPING.defense * levelsGained

    updatedPersonnage = {
      ...updatedPersonnage,
      niveau: newLevel,
      hp_max: newHpMax,
      mp_max: newMpMax,
      puissance: newPuissance,
      defense: newDefense,
      // Restaurer HP/MP au max lors d'un level up
      hp: newHpMax,
      mp: newMpMax,
    }

    // Mettre à jour dans la BD
    const { error } = await supabase!
      .from('personnage')
      .update({
        niveau: newLevel,
        experience: newExperience,
        hp_max: newHpMax,
        mp_max: newMpMax,
        puissance: newPuissance,
        defense: newDefense,
        hp: newHpMax,
        mp: newMpMax,
      })
      .eq('id_personnage', personnage.id_personnage)

    if (error) {
      throw new Error(`Erreur mise à jour niveau: ${error.message}`)
    }
  } else {
    // Juste mettre à jour l'XP
    const { error } = await supabase!
      .from('personnage')
      .update({ experience: newExperience })
      .eq('id_personnage', personnage.id_personnage)

    if (error) {
      throw new Error(`Erreur mise à jour XP: ${error.message}`)
    }
  }

  return {
    personnage: updatedPersonnage,
    leveledUp,
    newLevel: leveledUp ? newLevel : undefined,
  }
}

/**
 * Ajoute un pourcentage d'XP à un personnage
 */
export async function addPourcentExperience(
  personnage: Personnage,
  pourcentXP: number
): Promise<{ personnage: Personnage; leveledUp: boolean; newLevel?: number }> {
  // Récupérer les niveaux pour calculer l'XP nécessaire
  const { data: currentLevelData } = await supabase!
    .from('niveau')
    .select('experience')
    .eq('niveau', personnage.niveau)
    .single()

  const { data: nextLevelData } = await supabase!
    .from('niveau')
    .select('experience')
    .eq('niveau', personnage.niveau + 1)
    .single()

  if (!currentLevelData || !nextLevelData) {
    // Si pas de niveau suivant, pas d'XP à ajouter
    return { personnage, leveledUp: false }
  }

  const experienceForNextLevel = nextLevelData.experience - currentLevelData.experience
  const xpToAdd = Math.round((experienceForNextLevel * pourcentXP) / 100)

  return addExperience(personnage, xpToAdd)
}

/**
 * Soigne un personnage (remet HP et MP au maximum)
 */
export async function healPersonnage(personnage: Personnage): Promise<Personnage> {
  const { data, error } = await supabase!
    .from('personnage')
    .update({
      hp: personnage.hp_max,
      mp: personnage.mp_max,
    })
    .eq('id_personnage', personnage.id_personnage)
    .select()
    .single()

  if (error) {
    throw new Error(`Erreur soin personnage: ${error.message}`)
  }

  return data as Personnage
}

/**
 * Met à jour les HP/MP d'un personnage après combat
 */
export async function updatePersonnageStats(
  personnage: Personnage,
  hp: number,
  mp: number
): Promise<Personnage> {
  const { data, error } = await supabase!
    .from('personnage')
    .update({
      hp: Math.max(0, Math.min(hp, personnage.hp_max)),
      mp: Math.max(0, Math.min(mp, personnage.mp_max)),
    })
    .eq('id_personnage', personnage.id_personnage)
    .select()
    .single()

  if (error) {
    throw new Error(`Erreur mise à jour stats: ${error.message}`)
  }

  return data as Personnage
}

