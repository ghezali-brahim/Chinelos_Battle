import { supabase } from '../config/supabase'
import { Personnage } from '../types/models'

export const personnageService = {
  async getPersonnage(id_personnage: number): Promise<Personnage> {
    const { data, error } = await supabase
      .from('personnage')
      .select('*')
      .eq('id_personnage', id_personnage)
      .single()

    if (error) throw error
    return data
  },

  async getPersonnagesByEquipe(id_equipe: number): Promise<Personnage[]> {
    const { data, error } = await supabase
      .from('personnage')
      .select('*')
      .eq('id_equipe', id_equipe)
      .order('niveau', { ascending: false })

    if (error) throw error
    return data || []
  },

  async updatePersonnage(id_personnage: number, updates: Partial<Personnage>): Promise<Personnage> {
    const { data, error } = await supabase
      .from('personnage')
      .update(updates)
      .eq('id_personnage', id_personnage)
      .select()
      .single()

    if (error) throw error
    return data
  },
}

