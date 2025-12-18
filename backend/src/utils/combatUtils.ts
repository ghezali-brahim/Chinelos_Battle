import { Personnage, Element, Attaque } from '../types/models'

/**
 * Calcule les dégâts d'une attaque
 * Formule : (niveau * 0.4 + 4) * puissance / random(8-12)
 */
export function calculateDamage(
  personnage: Personnage,
  attaqueIndex: number,
  attaques: Attaque[]
): number {
  const attaquesIds = parseAttaques(personnage.attaques)
  const attaqueId = attaquesIds[attaqueIndex]
  const attaque = attaques.find(a => a.id_attaque === attaqueId)

  if (!attaque) return 0

  // Vérifier si assez de MP
  if (personnage.mp < attaque.mp_used) {
    throw new Error(`Pas assez de MP: ${personnage.mp}MP restant / ${attaque.mp_used}MP nécessaire`)
  }

  // Calcul des dégâts
  const randomMultiplier = Math.random() * 4 + 8 // Entre 8 et 12
  const degatsInfliges = ((personnage.niveau * 0.4 + 4) * personnage.puissance) / randomMultiplier

  return Math.max(1, Math.round(degatsInfliges))
}

/**
 * Applique la réduction de dégâts basée sur la défense
 * Réduction = défense * random(0.8-1.2)
 */
export function applyDefense(degats: number, defense: number): number {
  if (defense === 0) defense = 1

  const reduction = defense * (Math.random() * 0.4 + 0.8) // Entre 0.8 et 1.2
  const finalDamage = Math.max(1, Math.round(degats - reduction))

  return finalDamage
}

/**
 * Calcule le multiplicateur d'élément
 * Fort contre : 1.5x, Faible contre : 0.5x, Neutre : 1.0x
 */
export function getElementMultiplier(
  attackerElement: number,
  targetElement: number,
  elements: Element[]
): number {
  const attackerEl = elements.find(e => e.id_element === attackerElement)
  if (!attackerEl) return 1.0

  // Parse les éléments forts/faibles
  const fortsContre = attackerEl.id_fort_contre
    ? attackerEl.id_fort_contre.split(';').map(Number).filter(n => !isNaN(n))
    : []

  const faiblesContre = attackerEl.id_faible_contre
    ? attackerEl.id_faible_contre.split(';').map(Number).filter(n => !isNaN(n))
    : []

  if (fortsContre.includes(targetElement)) {
    return 1.5
  } else if (faiblesContre.includes(targetElement)) {
    return 0.5
  }

  return 1.0
}

/**
 * Parse la chaîne d'attaques "1;2;3" en tableau de nombres
 */
export function parseAttaques(attaquesString: string): number[] {
  if (!attaquesString) return []
  return attaquesString.split(';').map(id => parseInt(id)).filter(id => !isNaN(id))
}

/**
 * Applique les dégâts à un personnage
 */
export function applyDamage(personnage: Personnage, degats: number): Personnage {
  const newHp = Math.max(0, personnage.hp - degats)
  return {
    ...personnage,
    hp: newHp,
  }
}

/**
 * Retire les MP après une attaque
 */
export function consumeMP(personnage: Personnage, mpUsed: number): Personnage {
  if (personnage.mp < mpUsed) {
    throw new Error(`Pas assez de MP: ${personnage.mp}MP restant / ${mpUsed}MP nécessaire`)
  }

  return {
    ...personnage,
    mp: personnage.mp - mpUsed,
  }
}

/**
 * Vérifie si un personnage est mort
 */
export function isPersonnageDead(personnage: Personnage): boolean {
  return personnage.hp <= 0
}

/**
 * Vérifie si toute une équipe est morte
 */
export function isTeamDead(personnages: Personnage[]): boolean {
  return personnages.length === 0 || personnages.every(p => p.hp <= 0)
}

