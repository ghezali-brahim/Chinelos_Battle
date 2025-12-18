import axios from 'axios'
import { Item, InventaireItem } from '../types/models'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:5001/api'

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

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

export const boutiqueService = {
  async getItems(): Promise<Item[]> {
    const { data } = await api.get('/boutique/items')
    return data
  },

  async buyItem(params: any) {
    // Nettoyer et normaliser les paramètres
    let type: string
    let nom_personnage: string | undefined
    let id_element: number | undefined
    
    // Gérer le cas où type pourrait être imbriqué ou les données mal structurées
    if (typeof params === 'object' && params !== null) {
      if (typeof params.type === 'string') {
        type = params.type
        nom_personnage = params.nom_personnage
        id_element = params.id_element
      } else if (typeof params.type === 'object' && params.type !== null) {
        // Cas où type est un objet (bug de structure)
        const typeObj = params.type as any
        type = typeObj.type || 'soin'
        nom_personnage = typeObj.nom_personnage || params.nom_personnage
        id_element = typeObj.id_element || params.id_element
      } else {
        type = 'soin'
      }
    } else {
      type = 'soin'
    }
    
    // Construire le payload selon le type
    const payload: any = { type }
    
    if (type === 'personnage' && nom_personnage && id_element) {
      payload.nom_personnage = nom_personnage
      payload.id_element = id_element
    } else if (params.item_id) {
      // Pour les items
      payload.item_id = params.item_id
    }
    
    const response = await api.post('/boutique/buy', payload)
    return response.data
  },

  async getInventory(): Promise<InventaireItem[]> {
    const { data } = await api.get('/boutique/inventory')
    return data
  },
}

