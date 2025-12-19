/**
 * Générateur de noms de personnages - Thème Business/Entrepreneuriat
 */

// Prénoms de professionnels
const firstNames = [
  'Alexander', 'Benjamin', 'Charlotte', 'David', 'Emma', 'Felix',
  'Gabrielle', 'Henry', 'Isabella', 'James', 'Kate', 'Lucas',
  'Maya', 'Nathan', 'Olivia', 'Paul', 'Rachel', 'Samuel',
  'Sophia', 'Thomas', 'Victoria', 'William', 'Zoe', 'Daniel',
  'Emily', 'Michael', 'Anna', 'Christopher', 'Jessica', 'Matthew',
  'Sarah', 'Ryan', 'Lauren', 'Kevin', 'Amanda', 'Robert',
]

// Noms de famille professionnels
const lastNames = [
  'Anderson', 'Brown', 'Chen', 'Davis', 'Evans', 'Foster',
  'Garcia', 'Harris', 'Jackson', 'Kumar', 'Lee', 'Martinez',
  'Nguyen', 'Patel', 'Rodriguez', 'Smith', 'Taylor', 'Wilson',
  'Young', 'Zhang', 'Adams', 'Baker', 'Cooper', 'Dixon',
  'Edwards', 'Fletcher', 'Gibson', 'Hughes', 'Jones', 'Kelly',
  'Mitchell', 'Nelson', 'Parker', 'Reed', 'Stewart', 'Turner',
]

// Titres professionnels
const businessTitles = [
  'CEO', 'CTO', 'CFO', 'CMO', 'COO', 'Founder',
  'Director', 'Manager', 'Lead', 'VP', 'Head', 'Chief',
  'Executive', 'Senior', 'Principal', 'Partner', 'Owner', 'President',
]

// Spécialisations business
const businessRoles = [
  'Tech', 'Finance', 'Sales', 'Marketing', 'Operations', 'Strategy',
  'Innovation', 'Growth', 'Business', 'Product', 'Investment', 'Consulting',
  'Venture', 'Digital', 'Startup', 'Enterprise', 'Corporate', 'Analytics',
]

// Domaines d'expertise
const expertiseAreas = [
  'AI', 'FinTech', 'HealthTech', 'EdTech', 'Ecommerce', 'SaaS',
  'Blockchain', 'Mobile', 'Cloud', 'Data', 'Security', 'IoT',
  'Energy', 'RealEstate', 'Transport', 'Food', 'Retail', 'Media',
]

/**
 * Génère un nom de personnage aléatoire - Style Business/Entrepreneur
 * @param style Style du nom: 'full' | 'title' | 'role' | 'random'
 */
export function generateCharacterName(style: 'full' | 'title' | 'role' | 'random' = 'random'): string {
  const selectedStyle = style === 'random' 
    ? (['full', 'title', 'role'][Math.floor(Math.random() * 3)] as 'full' | 'title' | 'role')
    : style

  let name = ''

  switch (selectedStyle) {
    case 'full':
      // Prénom + Nom de famille
      const firstName = firstNames[Math.floor(Math.random() * firstNames.length)]
      const lastName = lastNames[Math.floor(Math.random() * lastNames.length)]
      name = `${firstName}_${lastName}`
      break
    
    case 'title':
      // Titre + Nom ou Titre + Spécialisation
      if (Math.random() > 0.5) {
        const title = businessTitles[Math.floor(Math.random() * businessTitles.length)]
        const lastName = lastNames[Math.floor(Math.random() * lastNames.length)]
        name = `${title}_${lastName}`
      } else {
        const title = businessTitles[Math.floor(Math.random() * businessTitles.length)]
        const role = businessRoles[Math.floor(Math.random() * businessRoles.length)]
        name = `${title}_${role}`
      }
      break
    
    case 'role':
      // Spécialisation + Nom ou Expertise + Titre
      if (Math.random() > 0.5) {
        const role = businessRoles[Math.floor(Math.random() * businessRoles.length)]
        const lastName = lastNames[Math.floor(Math.random() * lastNames.length)]
        name = `${role}_${lastName}`
      } else {
        const expertise = expertiseAreas[Math.floor(Math.random() * expertiseAreas.length)]
        const title = businessTitles[Math.floor(Math.random() * businessTitles.length)]
        name = `${expertise}_${title}`
      }
      break
    
    default:
      // Mélange de styles business
      const styles = [
        () => {
          const firstName = firstNames[Math.floor(Math.random() * firstNames.length)]
          const lastName = lastNames[Math.floor(Math.random() * lastNames.length)]
          return `${firstName}_${lastName}`
        },
        () => {
          const title = businessTitles[Math.floor(Math.random() * businessTitles.length)]
          const lastName = lastNames[Math.floor(Math.random() * lastNames.length)]
          return `${title}_${lastName}`
        },
        () => {
          const firstName = firstNames[Math.floor(Math.random() * firstNames.length)]
          const role = businessRoles[Math.floor(Math.random() * businessRoles.length)]
          return `${firstName}_${role}`
        },
        () => {
          const expertise = expertiseAreas[Math.floor(Math.random() * expertiseAreas.length)]
          const title = businessTitles[Math.floor(Math.random() * businessTitles.length)]
          return `${expertise}_${title}`
        },
        () => {
          const role = businessRoles[Math.floor(Math.random() * businessRoles.length)]
          const lastName = lastNames[Math.floor(Math.random() * lastNames.length)]
          return `${role}_${lastName}`
        },
      ]
      name = styles[Math.floor(Math.random() * styles.length)]()
      break
  }
  
  // Nettoyer le nom pour respecter la validation (alphanumériques et underscores uniquement)
  const cleanedName = name.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '')
  
  // S'assurer que le nom fait au moins 4 caractères
  if (cleanedName.length < 4) {
    return cleanedName.padEnd(4, 'X')
  }
  
  return cleanedName
}

/**
 * Génère plusieurs noms de personnages
 * @param count Nombre de noms à générer
 * @param style Style du nom
 */
export function generateMultipleNames(
  count: number = 5,
  style: 'full' | 'title' | 'role' | 'random' = 'random'
): string[] {
  const names = new Set<string>()
  let attempts = 0
  const maxAttempts = count * 10 // Limite pour éviter les boucles infinies
  
  while (names.size < count && attempts < maxAttempts) {
    const name = generateCharacterName(style)
    if (name.length >= 4) {
      names.add(name)
    }
    attempts++
  }
  
  // Si on n'a pas assez de noms uniques, en générer d'autres
  while (names.size < count) {
    const name = generateCharacterName('full')
    if (name.length >= 4) {
      names.add(name)
    }
  }
  
  return Array.from(names)
}

/**
 * Nettoie un nom pour respecter les contraintes de validation
 * Remplace les espaces par des underscores et garde seulement les caractères alphanumériques et underscores
 */
function sanitizeName(name: string): string {
  return name
    .replace(/\s+/g, '_') // Remplacer les espaces par des underscores
    .replace(/[^a-zA-Z0-9_]/g, '') // Supprimer les caractères non autorisés
    .substring(0, 50) // Limiter la longueur
}

/**
 * Génère un nom unique (évite les doublons dans une liste)
 * @param existingNames Liste des noms existants
 * @param maxAttempts Nombre maximum de tentatives
 */
export function generateUniqueName(
  existingNames: string[] = [],
  maxAttempts: number = 20,
  style: 'full' | 'title' | 'role' | 'random' = 'random'
): string {
  for (let i = 0; i < maxAttempts; i++) {
    const name = sanitizeName(generateCharacterName(style))
    if (name.length >= 4 && !existingNames.includes(name)) {
      return name
    }
  }
  
  // Si on n'a pas trouvé de nom unique, ajouter un suffixe numérique
  const baseName = sanitizeName(generateCharacterName(style))
  if (baseName.length < 4) {
    return baseName.padEnd(4, 'X')
  }
  
  let counter = 1
  let uniqueName = `${baseName}${counter}`
  
  while (existingNames.includes(uniqueName) && counter < 1000) {
    counter++
    uniqueName = `${baseName}${counter}`
  }
  
  return uniqueName
}

