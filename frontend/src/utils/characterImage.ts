/**
 * Utilitaires pour les images de personnages
 */

const CHARACTER_IMAGES_BASE = '/img/characters/'
const ELEMENT_IMAGES_BASE = '/img/element/'
const DEFAULT_CHARACTER_IMAGE = '/img/characters/default.png'

/**
 * Génère l'URL de l'image d'un personnage basée sur son élément et son niveau
 */
export function getCharacterImage(
  element: number,
  niveau: number,
  isEnemy: boolean = false
): string {
  // Pour les ennemis, utiliser des variantes différentes
  const prefix = isEnemy ? 'enemy' : 'hero'
  
  // Images basées sur l'élément
  const elementNames = ['normal', 'feu', 'eau', 'plante']
  const elementName = elementNames[element - 1] || 'normal'
  
  // Niveaux de variante (1-5, 6-10, 11+)
  const variant = niveau <= 5 ? '1' : niveau <= 10 ? '2' : '3'
  
  // Essayer différentes variantes d'images
  const possibleImages = [
    `${CHARACTER_IMAGES_BASE}${prefix}_${elementName}_${variant}.png`,
    `${CHARACTER_IMAGES_BASE}${prefix}_${elementName}.png`,
    `${CHARACTER_IMAGES_BASE}${prefix}_${variant}.png`,
    `${CHARACTER_IMAGES_BASE}${elementName}.png`,
    DEFAULT_CHARACTER_IMAGE,
  ]
  
  return possibleImages[0] // Pour l'instant, retourner la première option
  // En production, vérifier si l'image existe
}

/**
 * Retourne l'image d'un personnage avec fallback
 */
export function getCharacterImageWithFallback(
  element: number,
  niveau: number,
  isEnemy: boolean = false
): string {
  const image = getCharacterImage(element, niveau, isEnemy)
  
  // Si l'image n'existe pas, utiliser une image basée sur l'élément
  return image || `${ELEMENT_IMAGES_BASE}${element}.png`
}

/**
 * Génère un avatar CSS basé sur l'élément si pas d'image
 */
export function getCharacterAvatarStyle(element: number): React.CSSProperties {
  const colors: Record<number, string> = {
    1: '#9e9e9e', // Normal - gris
    2: '#f44336', // Feu - rouge
    3: '#2196f3', // Eau - bleu
    4: '#4caf50', // Plante - vert
  }
  
  return {
    backgroundColor: colors[element] || colors[1],
    backgroundImage: `url(${ELEMENT_IMAGES_BASE}${element}.png)`,
    backgroundSize: 'cover',
    backgroundPosition: 'center',
  }
}

