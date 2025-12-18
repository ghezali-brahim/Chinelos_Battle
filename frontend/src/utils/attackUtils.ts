/**
 * Utilitaires pour les attaques - icônes, couleurs, effets
 */

export function getAttackIcon(degats: number, mpUsed: number): string {
  // Basé sur les dégâts et le coût MP
  if (degats >= 50) return '💥' // Attaque puissante
  if (degats >= 30) return '⚡' // Attaque moyenne
  if (mpUsed === 0) return '👊' // Attaque gratuite
  return '✨' // Attaque magique
}

export function getAttackColor(degats: number, mpUsed: number): string {
  if (degats >= 50) return '#dc3545' // Rouge pour forte
  if (degats >= 30) return '#ff9800' // Orange pour moyenne
  if (mpUsed === 0) return '#28a745' // Vert pour gratuite
  return '#007bff' // Bleu pour magique
}

export function getAttackBackground(degats: number, mpUsed: number): string {
  const color = getAttackColor(degats, mpUsed)
  return `linear-gradient(135deg, ${color} 0%, ${adjustBrightness(color, -20)} 100%)`
}

export function getAttackType(degats: number, mpUsed: number): 'physical' | 'magic' | 'special' {
  if (mpUsed === 0) return 'physical'
  if (degats >= 50) return 'special'
  return 'magic'
}

function adjustBrightness(hex: string, percent: number): string {
  // Convertir hex en RGB
  const r = parseInt(hex.slice(1, 3), 16)
  const g = parseInt(hex.slice(3, 5), 16)
  const b = parseInt(hex.slice(5, 7), 16)

  // Ajuster la luminosité
  const newR = Math.max(0, Math.min(255, r + percent))
  const newG = Math.max(0, Math.min(255, g + percent))
  const newB = Math.max(0, Math.min(255, b + percent))

  // Reconvertir en hex
  return `#${[newR, newG, newB].map(x => {
    const hex = x.toString(16)
    return hex.length === 1 ? '0' + hex : hex
  }).join('')}`
}

