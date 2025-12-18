import { Personnage, Attaque, Niveau } from '../types/models'

export const parseAttaques = (attaquesString: string): number[] => {
  if (!attaquesString) return []
  return attaquesString.split(';').map(id => parseInt(id)).filter(id => !isNaN(id))
}

export const calculateDamage = (
  personnage: Personnage,
  attaqueIndex: number,
  attaques: Attaque[],
  targetPersonnage: Personnage
): number => {
  const attaquesIds = parseAttaques(personnage.attaques)
  const attaqueId = attaquesIds[attaqueIndex]
  const attaque = attaques.find(a => a.id_attaque === attaqueId)
  
  if (!attaque) return 0

  // Algorithme de calcul de dégâts similaire à PHP
  const degatsInfliges = (personnage.niveau * 0.4 + 4) * personnage.puissance / (Math.random() * 4 + 8)
  return Math.max(1, Math.round(degatsInfliges))
}

export const calculateDamageAfterDefense = (degats: number, defense: number): number => {
  if (defense === 0) defense = 1
  const reduction = defense * (Math.random() * 0.4 + 0.8) // Entre 0.8 et 1.2
  const finalDamage = Math.max(1, Math.round(degats - reduction))
  return finalDamage
}

export const isPersonnageDead = (personnage: Personnage): boolean => {
  return personnage.hp <= 0
}

export const getXpForNextLevel = (niveaux: Niveau[], currentLevel: number): number => {
  const nextLevel = niveaux.find(n => n.niveau === currentLevel + 1)
  return nextLevel?.experience || 0
}

export const getCurrentXpInLevel = (niveaux: Niveau[], personnage: Personnage): number => {
  const currentLevel = niveaux.find(n => n.niveau === personnage.niveau)
  const nextLevel = niveaux.find(n => n.niveau === personnage.niveau + 1)
  
  if (!currentLevel || !nextLevel) return 0
  return personnage.experience - currentLevel.experience
}

export const getXpNeededForNextLevel = (niveaux: Niveau[], personnage: Personnage): number => {
  const currentLevel = niveaux.find(n => n.niveau === personnage.niveau)
  const nextLevel = niveaux.find(n => n.niveau === personnage.niveau + 1)
  
  if (!currentLevel || !nextLevel) return 0
  return nextLevel.experience - personnage.experience
}

export const getXpPercentage = (niveaux: Niveau[], personnage: Personnage): number => {
  const currentXp = getCurrentXpInLevel(niveaux, personnage)
  const neededXp = getXpNeededForNextLevel(niveaux, personnage)
  
  if (neededXp === 0) return 100
  return Math.min(100, Math.max(0, (currentXp / neededXp) * 100))
}

