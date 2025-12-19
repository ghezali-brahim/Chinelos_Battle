// Types pour les modèles de données

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
  attaques: string // Format: "1;2;3"
  hp: number
  hp_max: number
  mp: number
  mp_max: number
  puissance: number
  defense: number
  id_equipe: number | null
  image_url?: string | null
}

export interface PersonnageWithAttaques extends Personnage {
  attaques_list: Attaque[]
}

export interface Equipe {
  id_equipe: number
  id_user: string
  personnages: Personnage[]
}

export interface Joueur {
  id_user: string
  username: string
  email: string
  argent: number
  nombre_victoire: number
  nombre_defaite: number
  connected: boolean
  last_connection: string
  equipes: Equipe[]
}

export interface Combat {
  id_combat: number
  id_joueur_1: string
  id_joueur_2: string | null
  indice_perso_j1: number
  indice_perso_j2: number
  valider_j1: boolean
  valider_j2: boolean
  finit: boolean
  nombre_tour: number
  indice_tour_de_joueur: number
  created_at: string
  updated_at: string
}

export interface Item {
  id_item: number
  nom: string
  description: string
  prix_achat: number
  type: number
  id_action: number
}

export interface InventaireItem {
  id_user: string
  id_item: number
  quantite: number
  item: Item
}

export interface Message {
  id_message: number
  objet: string
  contenu: string
  id_expeditaire: string
  id_destinataire: string
  date_envoie: string
  lu: boolean
}

export interface Niveau {
  niveau: number
  experience: number
}

export interface Action {
  id_action: number
  hp: number
  mp: number
  attaque: number
  defense: number
}

