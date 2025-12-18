import axios from 'axios'
import { Joueur, Equipe } from '../types/models'

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

export const joueurService = {
  async getProfile(): Promise<Joueur> {
    const { data } = await api.get('/joueur/profile')
    return data
  },

  async getStats() {
    const { data } = await api.get('/joueur/stats')
    return data
  },

  async getEquipes(): Promise<Equipe[]> {
    const { data } = await api.get('/joueur/equipes')
    return data
  },

  async getEquipeActive() {
    const { data } = await api.get('/joueur/equipe/active')
    return data
  },

  async getEquipeReserve() {
    const { data } = await api.get('/joueur/equipe/reserve')
    return data
  },

  async transferPersonnage(idPersonnage: number, toActive: boolean) {
    const { data } = await api.post('/joueur/equipe/transfer', {
      id_personnage: idPersonnage,
      toActive,
    })
    return data
  },

  async updateEquipe(id: number, updates: any) {
    const { data } = await api.put(`/joueur/equipe/${id}`, updates)
    return data
  },

  async getLeaderboard() {
    const { data } = await api.get('/joueur/leaderboard')
    return data
  },
}

