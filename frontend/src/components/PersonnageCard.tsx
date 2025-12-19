import { Personnage, Element, Attaque, Niveau } from '../types/models'
import { getElementIcon, getElementName } from '../utils/element'
import { parseAttaques, getXpPercentage, getXpNeededForNextLevel, getCurrentXpInLevel } from '../utils/personnage'
import { getBusinessAvatarIcon, getCharacterAvatarStyle } from '../utils/characterImage'
import { apiService } from '../services/api'
import { useState, useEffect } from 'react'
import './PersonnageCard.css'

interface PersonnageCardProps {
  personnage: Personnage
  elements: Element[]
  attaques: Attaque[]
  niveaux?: Niveau[]
  showAttacks?: boolean
  onAttackSelect?: (attackIndex: number) => void
  selected?: boolean
  onClick?: () => void
}

const PersonnageCard: React.FC<PersonnageCardProps> = ({
  personnage,
  elements,
  attaques,
  niveaux,
  showAttacks = false,
  onAttackSelect,
  selected = false,
  onClick,
}) => {
  const elementName = getElementName(elements, personnage.element)
  const elementIcon = getElementIcon(personnage.element)
  const attaquesIds = parseAttaques(personnage.attaques)
  const personnageAttaques = attaquesIds.map(id => attaques.find(a => a.id_attaque === id)).filter(Boolean) as Attaque[]

  const hpPercentage = (personnage.hp / personnage.hp_max) * 100
  const mpPercentage = (personnage.mp / personnage.mp_max) * 100

  // Calculer l'XP si les niveaux sont disponibles
  const xpPercentage = niveaux ? getXpPercentage(niveaux, personnage) : 0
  const xpNeeded = niveaux ? getXpNeededForNextLevel(niveaux, personnage) : 0
  const currentXpInLevel = niveaux ? getCurrentXpInLevel(niveaux, personnage) : 0

  // Avatar business
  const businessIcon = getBusinessAvatarIcon(personnage.element, personnage.niveau)
  const avatarStyle = getCharacterAvatarStyle(personnage.element, personnage.niveau, personnage.nom, false)
  const [generatedImage, setGeneratedImage] = useState<string | null>(personnage.image_url || null)
  const [imageLoading, setImageLoading] = useState(false)
  const [regenerating, setRegenerating] = useState(false)

  // Générer ou charger l'image du personnage
  useEffect(() => {
    const loadCharacterImage = async () => {
      if (personnage.image_url) {
        setGeneratedImage(personnage.image_url)
        return
      }

      if (!imageLoading && !generatedImage) {
        setImageLoading(true)
        try {
          const result = await apiService.getCharacterImage(personnage.id_personnage)
          if (result?.image_url) {
            setGeneratedImage(result.image_url)
          }
        } catch (error: any) {
          // Ne pas logger les erreurs si c'est juste qu'il n'y a pas de clé API configurée
          if (error?.response?.status !== 503) {
            console.warn('Impossible de charger/générer l\'image:', error)
          }
        } finally {
          setImageLoading(false)
        }
      }
    }

    loadCharacterImage()
  }, [personnage.id_personnage, personnage.image_url])

  // Fonction pour régénérer l'image
  const handleRegenerateImage = async (e: React.MouseEvent) => {
    e.stopPropagation() // Empêcher le clic de se propager
    setRegenerating(true)
    try {
      const result = await apiService.regenerateCharacterImage(personnage.id_personnage)
      if (result?.image_url) {
        // Forcer le re-render avec une nouvelle URL pour éviter le cache
        setGeneratedImage(null)
        setTimeout(() => setGeneratedImage(result.image_url + '?t=' + Date.now()), 100)
      }
    } catch (error: any) {
      console.error('Erreur lors de la régénération de l\'image:', error)
      const errorMessage = error?.response?.data?.error || 
                          error?.response?.status === 503
                            ? 'Aucune clé API configurée. Configurez OPENAI_API_KEY ou REPLICATE_API_TOKEN dans backend/.env'
                            : 'Impossible de régénérer l\'image. Vérifiez que les clés API sont configurées.'
      alert(errorMessage)
    } finally {
      setRegenerating(false)
    }
  }

  return (
    <div
      className={`personnage-card ${selected ? 'selected' : ''} ${personnage.hp <= 0 ? 'dead' : ''}`}
      onClick={onClick}
    >
      <div className="personnage-header">
        {generatedImage ? (
          <div className="business-avatar-small generated-avatar-small">
            <img
              src={generatedImage}
              alt={personnage.nom}
              className="character-generated-image-small"
              onError={() => setGeneratedImage(null)}
            />
            {imageLoading && <div className="image-loading-spinner-small">⏳</div>}
            <button
              className="regenerate-image-btn-small"
              onClick={handleRegenerateImage}
              title="Régénérer l'image"
              disabled={regenerating}
            >
              {regenerating ? '⏳' : '🔄'}
            </button>
          </div>
        ) : (
          <div className="business-avatar-small" style={avatarStyle}>
            <div className="business-icon-small">{businessIcon}</div>
            {imageLoading && <div className="image-loading-spinner-small">⏳</div>}
            {!imageLoading && (
              <button
                className="regenerate-image-btn-small"
                onClick={handleRegenerateImage}
                title="Générer une image"
                disabled={regenerating}
              >
                {regenerating ? '⏳' : '🎨'}
              </button>
            )}
          </div>
        )}
        <div className="personnage-info">
          <h4>{personnage.nom}</h4>
          <div className="personnage-meta">
            <span className="level">Niveau {personnage.niveau}</span>
            <span className="element-badge">
              <img src={elementIcon} alt={elementName} className="element-icon-small" />
              {elementName}
            </span>
          </div>
        </div>
      </div>

      <div className="personnage-stats">
        <div className="stat-bar">
          <label>HP</label>
          <div className="progress-bar">
            <div
              className="progress-fill hp"
              style={{ width: `${hpPercentage}%` }}
            />
          </div>
          <span>{personnage.hp} / {personnage.hp_max}</span>
        </div>

        <div className="stat-bar">
          <label>MP</label>
          <div className="progress-bar">
            <div
              className="progress-fill mp"
              style={{ width: `${mpPercentage}%` }}
            />
          </div>
          <span>{personnage.mp} / {personnage.mp_max}</span>
        </div>

        {niveaux && xpNeeded > 0 && (
          <div className="stat-bar xp-stat-bar">
            <label>⭐ XP</label>
            <div className="progress-bar">
              <div
                className="progress-fill xp"
                style={{ width: `${xpPercentage}%` }}
              />
            </div>
            <span>{currentXpInLevel} / {xpNeeded} XP</span>
            <div className="xp-level-hint">Niv. {personnage.niveau + 1} dans {xpNeeded} XP</div>
          </div>
        )}

        <div className="stat-row">
          <span>Puissance: {personnage.puissance}</span>
          <span>Défense: {personnage.defense}</span>
        </div>
      </div>

      {showAttacks && personnageAttaques.length > 0 && (
        <div className="personnage-attaques">
          <h5>Attaques:</h5>
          {personnageAttaques.map((attaque, index) => (
            <button
              key={attaque.id_attaque}
              className={`attack-button ${personnage.mp < attaque.mp_used ? 'disabled' : ''}`}
              onClick={(e) => {
                e.stopPropagation()
                if (onAttackSelect && personnage.mp >= attaque.mp_used) {
                  onAttackSelect(index)
                }
              }}
              disabled={personnage.mp < attaque.mp_used}
            >
              {attaque.nom} - {attaque.degats}% dmg ({attaque.mp_used} MP)
            </button>
          ))}
        </div>
      )}
    </div>
  )
}

export default PersonnageCard

