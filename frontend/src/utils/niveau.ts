import { Niveau } from '../types/models'

export const getLevelFromXp = (niveaux: Niveau[], xp: number): number => {
  // Trouver le niveau le plus élevé pour lequel l'xp est suffisant
  let level = 1
  for (const niveau of niveaux.sort((a, b) => a.niveau - b.niveau)) {
    if (xp >= niveau.experience) {
      level = niveau.niveau
    } else {
      break
    }
  }
  return level
}

export const getXpForLevel = (niveaux: Niveau[], level: number): number => {
  const niveau = niveaux.find(n => n.niveau === level)
  return niveau?.experience || 0
}

