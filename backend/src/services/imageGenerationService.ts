/**
 * Service de génération d'images de personnages business
 * Utilise Replicate API pour générer des images avec Stable Diffusion
 */

interface CharacterImagePrompt {
  nom: string
  element: number
  niveau: number
  isEnemy?: boolean
}

// Mapping des éléments vers des descripteurs business
const elementDescriptions: Record<number, { 
  color: string
  style: string
  personality: string
  specialty: string 
}> = {
  1: { // Normal
    color: 'navy blue or charcoal gray',
    style: 'classic executive suit',
    personality: 'professional and confident',
    specialty: 'business executive'
  },
  2: { // Feu
    color: 'burgundy red or crimson',
    style: 'bold power suit with modern cut',
    personality: 'dynamic and ambitious entrepreneur',
    specialty: 'tech startup founder'
  },
  3: { // Eau
    color: 'electric blue or deep navy',
    style: 'sleek contemporary business attire',
    personality: 'innovative and strategic',
    specialty: 'technology innovator'
  },
  4: { // Plante
    color: 'forest green or emerald',
    style: 'refined business casual with blazer',
    personality: 'growth-focused and visionary',
    specialty: 'growth CEO'
  },
}

// Styles selon le niveau
const levelStyles: Record<string, string> = {
  junior: 'young professional, casual business attire, startup environment',
  senior: 'mature executive, formal business suit, corporate office',
  executive: 'top CEO, luxury business suit, prestigious boardroom',
}

/**
 * Génère un prompt pour la génération d'image
 */
function generateImagePrompt({ nom, element, niveau, isEnemy = false }: CharacterImagePrompt): string {
  const elementDesc = elementDescriptions[element] || elementDescriptions[1]
  const levelStyle = niveau <= 5 ? levelStyles.junior : niveau <= 10 ? levelStyles.senior : levelStyles.executive
  const enemyModifier = isEnemy ? ', with a more competitive and aggressive expression' : ', with a professional and friendly expression'
  
  return `Professional business portrait of ${nom}, a ${elementDesc.specialty}, wearing a ${elementDesc.color} ${elementDesc.style}. ${levelStyle}. ${elementDesc.personality}${enemyModifier}. High quality, photorealistic, business portrait photography, professional headshot, clean background, corporate setting, modern office, 4k resolution, detailed facial features, professional lighting`
}

/**
 * Génère une image via Replicate API (Stable Diffusion)
 * Nécessite REPLICATE_API_TOKEN dans les variables d'environnement
 */
export async function generateCharacterImage(
  prompt: CharacterImagePrompt
): Promise<string | null> {
  const replicateApiToken = process.env.REPLICATE_API_TOKEN
  
  if (!replicateApiToken) {
    console.warn('⚠️  REPLICATE_API_TOKEN non configuré. Utilisation du fallback.')
    return null
  }

  try {
    // Import dynamique de Replicate pour éviter les erreurs si non installé
    const Replicate = (await import('replicate')).default
    const replicate = new Replicate({ auth: replicateApiToken })

    const imagePrompt = generateImagePrompt(prompt)
    
    // Utiliser Stable Diffusion XL pour de meilleures images
    const output = await replicate.run(
      "stability-ai/sdxl:39ed52f2a78e934b3ba6e2a89f5b1c712de7dfea535525255b1aa35c5565e08b",
      {
        input: {
          prompt: imagePrompt,
          num_outputs: 1,
          aspect_ratio: "1:1",
          output_format: "url",
          output_quality: 95,
        }
      }
    ) as string[]

    // Retourner la première image générée
    if (output && output.length > 0 && typeof output[0] === 'string') {
      return output[0]
    }

    return null
  } catch (error) {
    console.error('Erreur lors de la génération d\'image:', error)
    return null
  }
}

/**
 * Génère une image via l'API OpenAI DALL-E 3
 * DALL-E 3 produit des images de très haute qualité et réalistes
 */
export async function generateCharacterImageWithDalle(
  prompt: CharacterImagePrompt
): Promise<string | null> {
  // Accepter OPENAI_API_KEY ou OPEN_API_KEY (pour compatibilité)
  const openaiApiKey = process.env.OPENAI_API_KEY || process.env.OPEN_API_KEY
  
  if (!openaiApiKey) {
    console.warn('⚠️  OPENAI_API_KEY non configuré.')
    return null
  }

  try {
    // Import dynamique
    const openaiModule = await import('openai')
    const OpenAI = openaiModule.default || openaiModule
    const openai = new OpenAI({ apiKey: openaiApiKey })

    const imagePrompt = generateImagePrompt(prompt)
    
    console.log('📝 Prompt DALL-E:', imagePrompt.substring(0, 150) + '...')
    
    const response = await openai.images.generate({
      model: "dall-e-3",
      prompt: imagePrompt,
      n: 1,
      size: "1024x1024",
      quality: "hd", // HD pour meilleure qualité
    })

    if (response.data && response.data.length > 0 && response.data[0].url) {
      const imageUrl = response.data[0].url
      console.log('✅ DALL-E 3: Image générée avec succès')
      return imageUrl
    }

    return null
  } catch (error: any) {
    console.error('❌ Erreur lors de la génération d\'image avec DALL-E:', error?.message || error)
    
    // Gérer les erreurs spécifiques
    if (error?.response?.status === 429) {
      console.error('⚠️  Rate limit atteint pour OpenAI. Attendez quelques instants.')
    } else if (error?.response?.status === 401) {
      console.error('⚠️  Clé API OpenAI invalide. Vérifiez OPENAI_API_KEY dans backend/.env')
    } else if (error?.message?.includes('content_policy_violation')) {
      console.error('⚠️  Prompt rejeté par OpenAI (politique de contenu). Tentative avec un prompt modifié...')
      // Réessayer avec un prompt plus simple
      const simplifiedPrompt: CharacterImagePrompt = {
        ...prompt,
        nom: prompt.nom.replace(/[^a-zA-Z0-9_]/g, ' '), // Nettoyer le nom
      }
      return generateCharacterImageWithDalle(simplifiedPrompt)
    }
    
    return null
  }
}

/**
 * Génère une image via l'API Gemini (Google) - pour les images, utilise Imagen
 * Note: Gemini génère principalement du texte, mais Google a Imagen pour les images
 */
export async function generateCharacterImageWithImagen(
  prompt: CharacterImagePrompt
): Promise<string | null> {
  // Cette fonction nécessiterait l'API Google Cloud Imagen
  // Pour l'instant, on retourne null
  console.warn('⚠️  Imagen API non implémentée. Utilisez Replicate ou DALL-E.')
  return null
}

/**
 * Fonction principale qui essaie différentes APIs selon la configuration
 * Priorité: DALL-E (OpenAI) > Replicate (Stable Diffusion)
 */
export async function generateCharacterImageAuto(
  prompt: CharacterImagePrompt
): Promise<string | null> {
  console.log('🎨 Génération d\'image pour:', prompt.nom, '- Élément:', prompt.element, '- Niveau:', prompt.niveau)
  
  // Essayer DALL-E d'abord (meilleure qualité)
  if (process.env.OPENAI_API_KEY || process.env.OPEN_API_KEY) {
    console.log('✨ Utilisation de OpenAI DALL-E 3...')
    try {
      const dalleImage = await generateCharacterImageWithDalle(prompt)
      if (dalleImage) {
        console.log('✅ Image générée avec succès via DALL-E:', dalleImage.substring(0, 50) + '...')
        return dalleImage
      }
    } catch (error) {
      console.warn('⚠️  Erreur avec DALL-E, essai avec Replicate...', error)
    }
  }

  // Sinon essayer Replicate
  if (process.env.REPLICATE_API_TOKEN) {
    console.log('✨ Utilisation de Replicate (Stable Diffusion)...')
    try {
      const replicateImage = await generateCharacterImage(prompt)
      if (replicateImage) {
        console.log('✅ Image générée avec succès via Replicate:', replicateImage.substring(0, 50) + '...')
        return replicateImage
      }
    } catch (error) {
      console.warn('⚠️  Erreur avec Replicate:', error)
    }
  }

  // Si aucune API n'est configurée, retourner null
  const hasOpenAI = !!(process.env.OPENAI_API_KEY || process.env.OPEN_API_KEY)
  if (!hasOpenAI && !process.env.REPLICATE_API_TOKEN) {
    console.warn('⚠️  Aucune clé API configurée (OPENAI_API_KEY/OPEN_API_KEY ou REPLICATE_API_TOKEN)')
  }
  
  return null
}

