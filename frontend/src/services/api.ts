import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:5000/api'

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Intercepteur pour ajouter le token d'authentification
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('supabase.auth.token')
  if (token) {
    try {
      const authData = JSON.parse(token)
      if (authData.access_token) {
        config.headers.Authorization = `Bearer ${authData.access_token}`
      }
    } catch (e) {
      // Ignore
    }
  }
  return config
})

// Fonctions API
export const apiService = {
  async getElements() {
    const { data } = await api.get('/element')
    return data
  },

  async getAttaques() {
    const { data } = await api.get('/attaque')
    return data
  },

  async getNiveaux() {
    const { data } = await api.get('/niveau')
    return data
  },

  async generateCharacterImage(idPersonnage: number, element: number, niveau: number, nom: string, isEnemy: boolean = false) {
    const { data } = await api.post('/images/generate', {
      id_personnage: idPersonnage,
      element,
      niveau,
      nom,
      isEnemy,
    })
    return data
  },

  async getCharacterImage(idPersonnage: number) {
    const { data } = await api.get(`/images/character/${idPersonnage}`)
    return data
  },

  async regenerateCharacterImage(idPersonnage: number) {
    const { data } = await api.post(`/images/regenerate/${idPersonnage}`)
    return data
  },
}

export default api
