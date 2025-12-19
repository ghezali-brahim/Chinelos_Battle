/**
 * Utilitaires pour les avatars de personnages Business/Entrepreneurs
 */

const ELEMENT_IMAGES_BASE = '/img/element/'

// Types d'avatars business selon l'élément et le niveau
const businessAvatarTypes = {
  1: { // Normal - Executive classique
    suit: '#2c3e50', // Bleu marine
    shirt: '#ecf0f1', // Blanc
    tie: '#34495e', // Gris foncé
    icon: '👔',
  },
  2: { // Feu - Entrepreneur dynamique
    suit: '#c0392b', // Rouge foncé
    shirt: '#e74c3c', // Rouge
    tie: '#d32f2f', // Rouge vif
    icon: '🔥',
  },
  3: { // Eau - Innovateur Tech
    suit: '#2980b9', // Bleu
    shirt: '#3498db', // Bleu clair
    tie: '#1976d2', // Bleu moyen
    icon: '💼',
  },
  4: { // Plante - CEO Growth
    suit: '#27ae60', // Vert foncé
    shirt: '#2ecc71', // Vert
    tie: '#1b5e20', // Vert très foncé
    icon: '📈',
  },
}

/**
 * Génère un style d'avatar business basé sur l'élément, le niveau et le nom
 */
export function getCharacterAvatarStyle(
  element: number,
  niveau: number = 1,
  nom: string = '',
  isEnemy: boolean = false
): React.CSSProperties {
  const avatarConfig = businessAvatarTypes[element as keyof typeof businessAvatarTypes] || businessAvatarTypes[1]
  
  // Générer une couleur unique basée sur le nom pour la variété
  const nameHash = nom.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0)
  const hue = (nameHash * 137.508) % 360 // Golden angle pour distribution uniforme
  
  // Niveau influence la nuance (plus élevé = plus riche)
  const saturation = Math.min(40 + niveau * 2, 70)
  const lightness = isEnemy ? 55 : 50 + Math.min(niveau * 1.5, 15)
  
  const baseColor = `hsl(${hue}, ${saturation}%, ${lightness}%)`
  
  return {
    background: `linear-gradient(135deg, ${avatarConfig.suit} 0%, ${baseColor} 100%)`,
    position: 'relative',
    overflow: 'hidden',
  }
}

/**
 * Génère un avatar SVG personnalisé pour un personnage business
 */
export function getBusinessAvatarSVG(
  element: number,
  niveau: number = 1,
  nom: string = '',
  isEnemy: boolean = false
): string {
  const avatarConfig = businessAvatarTypes[element as keyof typeof businessAvatarTypes] || businessAvatarTypes[1]
  const nameHash = nom.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0)
  const initial = nom.charAt(0).toUpperCase() || 'A'
  
  // Style selon le niveau (junior, senior, executive)
  const levelStyle = niveau <= 5 ? 'junior' : niveau <= 10 ? 'senior' : 'executive'
  
  // SVG avec un style business
  return `
    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="suitGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" style="stop-color:${avatarConfig.suit};stop-opacity:1" />
          <stop offset="100%" style="stop-color:${avatarConfig.shirt};stop-opacity:1" />
        </linearGradient>
        <linearGradient id="tieGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" style="stop-color:${avatarConfig.tie};stop-opacity:1" />
          <stop offset="100%" style="stop-color:${avatarConfig.suit};stop-opacity:1" />
        </linearGradient>
      </defs>
      <!-- Corps (veste) -->
      <rect x="25" y="35" width="50" height="60" fill="url(#suitGrad)" rx="5"/>
      <!-- Chemise -->
      <rect x="30" y="40" width="40" height="50" fill="${avatarConfig.shirt}" rx="2"/>
      <!-- Cravate -->
      <path d="M 45 40 L 50 50 L 45 70 L 55 70 L 50 50 Z" fill="url(#tieGrad)"/>
      <!-- Tête -->
      <circle cx="50" cy="30" r="15" fill="#fdbcb4"/>
      <!-- Initiale -->
      <text x="50" y="36" font-family="Arial, sans-serif" font-size="20" font-weight="bold" 
            text-anchor="middle" fill="${avatarConfig.suit}">${initial}</text>
      ${levelStyle === 'executive' ? '<rect x="40" y="25" width="20" height="3" fill="#333" rx="1"/>' : ''}
    </svg>
  `
}

/**
 * Génère un emoji/icône business selon l'élément et le niveau
 */
export function getBusinessAvatarIcon(element: number, niveau: number = 1): string {
  const avatarConfig = businessAvatarTypes[element as keyof typeof businessAvatarTypes] || businessAvatarTypes[1]
  
  // Icônes selon le niveau
  if (niveau <= 5) {
    return '👤' // Junior
  } else if (niveau <= 10) {
    return avatarConfig.icon // Senior (🔥, 💼, 📈)
  } else {
    return '👨‍💼' // Executive/CEO
  }
}

/**
 * Retourne l'image d'un personnage avec fallback (maintenu pour compatibilité)
 */
export function getCharacterImageWithFallback(
  element: number,
  niveau: number,
  isEnemy: boolean = false
): string {
  // Pour les avatars business, on utilise principalement les styles CSS/SVG
  return ELEMENT_IMAGES_BASE ? `${ELEMENT_IMAGES_BASE}${element}.png` : ''
}

