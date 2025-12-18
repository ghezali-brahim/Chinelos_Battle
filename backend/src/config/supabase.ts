import { createClient } from '@supabase/supabase-js'

const supabaseUrl = process.env.SUPABASE_URL || ''
const supabaseServiceRoleKey = process.env.SUPABASE_SERVICE_ROLE_KEY || ''
const supabaseAnonKey = process.env.SUPABASE_ANON_KEY || ''

// Client avec service role key pour le backend (bypass RLS)
// Ne pas throw d'erreur si les variables manquent au démarrage pour éviter les crashes
export const supabase = supabaseUrl && supabaseServiceRoleKey
  ? createClient(supabaseUrl, supabaseServiceRoleKey, {
      auth: {
        autoRefreshToken: false,
        persistSession: false
      }
    })
  : null

// Client public pour les opérations qui respectent RLS
export const supabasePublic = supabaseUrl && supabaseAnonKey
  ? createClient(supabaseUrl, supabaseAnonKey)
  : null

// Fonction pour vérifier la configuration
export const checkSupabaseConfig = () => {
  if (!supabaseUrl || !supabaseServiceRoleKey || !supabaseAnonKey) {
    console.warn('⚠️  Supabase configuration manquante. Vérifiez vos variables d\'environnement dans backend/.env')
    return false
  }
  return true
}

