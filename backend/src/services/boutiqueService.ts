import { supabase } from '../config/supabase'
import { createPersonnage } from './personnageService'
import { Personnage } from '../types/models'
import { Personnage as PersonnageType } from '../types/models'

/**
 * Acheter un personnage (5 gils)
 */
export async function buyPersonnage(
  userId: string,
  nom: string,
  id_element: number,
  id_equipe: number
): Promise<{ personnage: PersonnageType; newBalance: number }> {
  const PRICE = 5

  // Vérifier le solde
  const { data: user } = await supabase!
    .from('users')
    .select('argent')
    .eq('id_user', userId)
    .single()

  if (!user) {
    throw new Error('Utilisateur non trouvé')
  }

  if (user.argent < PRICE) {
    throw new Error(`Vous ne possédez pas suffisamment de gils. Nécessaire: ${PRICE}, Vous avez: ${user.argent}`)
  }

  // Validation du nom
  if (!/^[a-zA-Z0-9_]{4,}$/.test(nom)) {
    throw new Error('Le nom doit contenir au moins 4 caractères alphanumériques ou underscores')
  }

  // Créer le personnage
  try {
    const personnage = await createPersonnage(1, nom, id_element, id_equipe)

    // Débiter l'argent
    const newBalance = user.argent - PRICE
    const { error: updateError } = await supabase!
      .from('users')
      .update({ argent: newBalance })
      .eq('id_user', userId)

    if (updateError) {
      // Rollback : supprimer le personnage créé
      await supabase!
        .from('personnage')
        .delete()
        .eq('id_personnage', personnage.id_personnage)
      
      throw new Error(`Erreur lors du paiement: ${updateError.message}`)
    }

    return { personnage, newBalance }
  } catch (error: any) {
    throw new Error(`Erreur création personnage: ${error.message}`)
  }
}

/**
 * Soigner toute l'équipe (1 gils)
 */
export async function buyHeal(userId: string, id_equipe: number): Promise<{ newBalance: number; healedCount: number }> {
  const PRICE = 1

  // Vérifier le solde
  const { data: user } = await supabase!
    .from('users')
    .select('argent')
    .eq('id_user', userId)
    .single()

  if (!user) {
    throw new Error('Utilisateur non trouvé')
  }

  if (user.argent < PRICE) {
    throw new Error(`Vous ne possédez pas suffisamment de gils. Nécessaire: ${PRICE}, Vous avez: ${user.argent}`)
  }

  // Récupérer tous les personnages de l'équipe
  const { data: personnages } = await supabase!
    .from('personnage')
    .select('*')
    .eq('id_equipe', id_equipe)

  if (!personnages || personnages.length === 0) {
    throw new Error('Aucun personnage dans cette équipe')
  }

  // Soigner tous les personnages
  const healPromises = personnages.map((p: PersonnageType) =>
    supabase!
      .from('personnage')
      .update({
        hp: p.hp_max,
        mp: p.mp_max,
      })
      .eq('id_personnage', p.id_personnage)
  )

  await Promise.all(healPromises)

  // Débiter l'argent
  const newBalance = user.argent - PRICE
  const { error: updateError } = await supabase!
    .from('users')
    .update({ argent: newBalance })
    .eq('id_user', userId)

  if (updateError) {
    throw new Error(`Erreur lors du paiement: ${updateError.message}`)
  }

  return { newBalance, healedCount: personnages.length }
}

