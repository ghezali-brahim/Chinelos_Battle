import api from './api'
import { EnemyOption } from '../components/EnemySelector'
import { LogEntry } from '../components/CombatLog'

export interface CombatState {
  id_combat: number
  joueur_team: any[]
  enemy_team: any[]
  current_turn: 'joueur' | 'enemy'
  current_personnage_index_joueur: number
  current_personnage_index_enemy: number
  nombre_tour: number
  finit: boolean
  winner?: 'joueur' | 'enemy'
}

export interface AttackResponse {
  combatState: CombatState
  attackResult: {
    attacker: any
    target: any
    damage: number
    elementMultiplier: number
    targetHpAfter: number
    success: boolean
    error?: string
  }
  aiAttackResult?: {
    attacker: any
    target: any
    damage: number
    elementMultiplier: number
    targetHpAfter: number
    success: boolean
  } | null
  rewards?: {
    argent: number
    xpPoints: number
    newBalance: number
  }
}

export const combatService = {
  async getEnemiesList(): Promise<EnemyOption[]> {
    const { data } = await api.get('/combat/enemies/list')
    return data
  },

  async createCombat(enemyLevel: number | null = null): Promise<CombatState> {
    const { data } = await api.post('/combat/create', { enemyLevel })
    return data
  },

  async getCombat(id: number): Promise<CombatState> {
    const { data } = await api.get(`/combat/${id}`)
    return data
  },

  async executeAttack(
    id: number,
    indice_attaque: number,
    indice_perso_enemy: number
  ): Promise<AttackResponse> {
    const { data } = await api.post(`/combat/${id}/attack`, {
      indice_attaque,
      indice_perso_enemy,
    })
    return data
  },
}
