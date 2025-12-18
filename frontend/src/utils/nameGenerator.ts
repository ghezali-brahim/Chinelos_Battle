/**
 * Générateur de noms de personnages
 */

const prefixes = [
  'Ace', 'Blade', 'Crimson', 'Dark', 'Echo', 'Flame', 'Ghost', 'Hawk',
  'Iron', 'Jade', 'Knight', 'Light', 'Moon', 'Night', 'Ocean', 'Phoenix',
  'Quick', 'Raven', 'Shadow', 'Thunder', 'Ultra', 'Void', 'Wild', 'Zen',
  'Alpha', 'Beta', 'Gamma', 'Delta', 'Sigma', 'Omega',
  'Storm', 'Frost', 'Blaze', 'Tide', 'Stone', 'Wind',
]

const suffixes = [
  'Warrior', 'Mage', 'Knight', 'Rogue', 'Guardian', 'Assassin',
  'Sorcerer', 'Paladin', 'Ranger', 'Monk', 'Druid', 'Bard',
  'Fighter', 'Wizard', 'Barbarian', 'Ninja', 'Samurai', 'Viking',
  'Dragon', 'Tiger', 'Wolf', 'Eagle', 'Lion', 'Bear',
  'Storm', 'Flame', 'Ice', 'Thunder', 'Shadow', 'Light',
  'Blade', 'Shield', 'Arrow', 'Spear', 'Axe', 'Bow',
  'Master', 'Lord', 'King', 'Queen', 'Prince', 'Princess',
]

const middleWords = [
  'the', 'of', 'Dark', 'Light', 'Fire', 'Ice', 'Storm', 'Thunder',
  'Shadow', 'Golden', 'Silver', 'Iron', 'Steel', 'Crystal',
]

const fantasyNames = [
  'Aragorn', 'Legolas', 'Gandalf', 'Frodo', 'Bilbo', 'Sauron',
  'Gimli', 'Boromir', 'Merry', 'Pippin', 'Samwise', 'Gollum',
  'Thorin', 'Balin', 'Dwalin', 'Fili', 'Kili', 'Bofur',
  'Eragon', 'Murtagh', 'Arya', 'Roran', 'Nasuada', 'Galbatorix',
  'Geralt', 'Ciri', 'Yennefer', 'Triss', 'Dandelion', 'Zoltan',
  'Link', 'Zelda', 'Ganondorf', 'Impa', 'Sheik', 'Navi',
  'Cloud', 'Tifa', 'Aerith', 'Sephiroth', 'Vincent', 'Yuffie',
]

const simpleNames = [
  'Alex', 'Jordan', 'Casey', 'Morgan', 'Riley', 'Avery',
  'Quinn', 'Cameron', 'Dakota', 'Sage', 'River', 'Sky',
  'Phoenix', 'Storm', 'Blaze', 'Frost', 'Shadow', 'Light',
  'Kai', 'Zane', 'Luna', 'Nova', 'Orion', 'Vega',
]

/**
 * Génère un nom de personnage aléatoire
 * @param style Style du nom: 'fantasy' | 'simple' | 'compound' | 'random'
 */
export function generateCharacterName(style: 'fantasy' | 'simple' | 'compound' | 'random' = 'random'): string {
  const selectedStyle = style === 'random' 
    ? (['fantasy', 'simple', 'compound'][Math.floor(Math.random() * 3)] as 'fantasy' | 'simple' | 'compound')
    : style

  let name = ''

  switch (selectedStyle) {
    case 'fantasy':
      name = fantasyNames[Math.floor(Math.random() * fantasyNames.length)]
      break
    
    case 'simple':
      name = simpleNames[Math.floor(Math.random() * simpleNames.length)]
      break
    
    case 'compound':
      // Préfixe + Suffixe
      const prefix = prefixes[Math.floor(Math.random() * prefixes.length)]
      const suffix = suffixes[Math.floor(Math.random() * suffixes.length)]
      name = `${prefix}${suffix}`
      break
    
    default:
      // Mélange de styles
      const styles = [
        () => fantasyNames[Math.floor(Math.random() * fantasyNames.length)],
        () => simpleNames[Math.floor(Math.random() * simpleNames.length)],
        () => {
          const prefix = prefixes[Math.floor(Math.random() * prefixes.length)]
          const suffix = suffixes[Math.floor(Math.random() * suffixes.length)]
          return `${prefix}${suffix}`
        },
        () => {
          // Préfixe + Mot du milieu + Suffixe (sans espaces)
          const prefix = prefixes[Math.floor(Math.random() * prefixes.length)]
          const middle = middleWords[Math.floor(Math.random() * middleWords.length)].replace(/\s+/g, '')
          const suffix = suffixes[Math.floor(Math.random() * suffixes.length)]
          return `${prefix}${middle}${suffix}`
        },
        () => {
          // Nom simple + Suffixe (sans espaces)
          const name = simpleNames[Math.floor(Math.random() * simpleNames.length)]
          const suffix = suffixes[Math.floor(Math.random() * suffixes.length)]
          return `${name}${suffix}`
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
  style: 'fantasy' | 'simple' | 'compound' | 'random' = 'random'
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
    const name = generateCharacterName('compound')
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
  style: 'fantasy' | 'simple' | 'compound' | 'random' = 'random'
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

