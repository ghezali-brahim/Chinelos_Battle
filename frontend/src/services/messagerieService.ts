import axios from 'axios'
import { Message } from '../types/models'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:5000/api'

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

export const messagerieService = {
  async getMessages(): Promise<{ envoyes: Message[]; recus: Message[] }> {
    const { data } = await api.get('/messagerie/messages')
    return data
  },

  async sendMessage(objet: string, contenu: string, id_destinataire: string): Promise<Message> {
    const { data } = await api.post('/messagerie/send', {
      objet,
      contenu,
      id_destinataire,
    })
    return data
  },

  async markAsRead(id: number): Promise<Message> {
    const { data } = await api.put(`/messagerie/${id}/read`)
    return data
  },
}

