// Types pour les modèles de données (backend)

export interface Element {
  id_element: number
  nom: string
  id_faible_contre: string | null
  id_fort_contre: string | null
}

export interface Attaque {
  id_attaque: number
  nom: string
  degats: number
  mp_used: number
}

export interface Personnage {
  id_personnage: number
  nom: string
  element: number
  niveau: number
  experience: number
  attaques: string
  hp: number
  hp_max: number
  mp: number
  mp_max: number
  puissance: number
  defense: number
  id_equipe: number | null
}

export interface Equipe {
  id_equipe: number
  id_user: string
  personnages: Personnage[]
}

export interface Niveau {
  niveau: number
  experience: number
}

