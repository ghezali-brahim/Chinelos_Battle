import { Personnage, Element, Niveau } from '../types/models'
import { getElementIcon, getElementName } from '../utils/element'
import { getBusinessAvatarIcon, getCharacterAvatarStyle } from '../utils/characterImage'
import { getXpPercentage, getXpNeededForNextLevel, getCurrentXpInLevel } from '../utils/personnage'
import { apiService } from '../services/api'
import { useState, useEffect } from 'react'
import './CombatCharacter.css'

interface CombatCharacterProps {
  personnage: Personnage
  elements: Element[]
  niveaux?: Niveau[]
  isEnemy?: boolean
  isActive?: boolean
  isSelected?: boolean
  onClick?: () => void
  showActions?: boolean
  onAttackClick?: () => void
}

const CombatCharacter = ({
  personnage,
  elements,
  niveaux,
  isEnemy = false,
  isActive = false,
  isSelected = false,
  onClick,
  showActions = false,
  onAttackClick,
}: CombatCharacterProps) => {
  const elementName = getElementName(elements, personnage.element)
  const elementIcon = getElementIcon(personnage.element)
  const hpPercentage = (personnage.hp / personnage.hp_max) * 100
  const mpPercentage = (personnage.mp / personnage.mp_max) * 100
  const isDead = personnage.hp <= 0

  // Calculer l'XP si les niveaux sont disponibles
  const xpPercentage = niveaux ? getXpPercentage(niveaux, personnage) : 0
  const xpNeeded = niveaux ? getXpNeededForNextLevel(niveaux, personnage) : 0
  const currentXpInLevel = niveaux ? getCurrentXpInLevel(niveaux, personnage) : 0

  const businessIcon = getBusinessAvatarIcon(personnage.element, personnage.niveau)
  const avatarStyle = getCharacterAvatarStyle(personnage.element, personnage.niveau, personnage.nom, isEnemy)
  const [generatedImage, setGeneratedImage] = useState<string | null>(personnage.image_url || null)
  const [imageLoading, setImageLoading] = useState(false)
  const [regenerating, setRegenerating] = useState(false)

  // Générer ou charger l'image du personnage
  useEffect(() => {
    const loadCharacterImage = async () => {
      // Si l'image existe déjà, l'utiliser
      if (personnage.image_url) {
        setGeneratedImage(personnage.image_url)
        return
      }

      // Sinon, essayer de la générer
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
            console.warn('Impossible de charger/générer l\'image du personnage:', error)
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
        setGeneratedImage(result.image_url)
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
      className={`combat-character ${isEnemy ? 'enemy' : 'ally'} ${isActive ? 'active' : ''} ${isSelected ? 'selected' : ''} ${isDead ? 'dead' : ''}`}
      onClick={onClick}
    >
      <div className="character-image-wrapper">
        {generatedImage ? (
          <div className="character-avatar business-avatar generated-avatar">
            <img
              src={generatedImage}
              alt={personnage.nom}
              className="character-generated-image"
              onError={() => {
                // Fallback vers l'avatar emoji si l'image ne charge pas
                setGeneratedImage(null)
              }}
            />
            {isDead && <div className="death-overlay">💀</div>}
            {!isDead && (
              <img
                src={elementIcon}
                alt={elementName}
                className="element-overlay-small"
              />
            )}
            <button
              className="regenerate-image-btn"
              onClick={handleRegenerateImage}
              title="Régénérer l'image"
              disabled={regenerating}
            >
              {regenerating ? '⏳' : '🔄'}
            </button>
          </div>
        ) : (
          <div
            className="character-avatar business-avatar"
            style={avatarStyle}
          >
            <div className="business-icon">{businessIcon}</div>
            {!isDead && (
              <img
                src={elementIcon}
                alt={elementName}
                className="element-overlay"
              />
            )}
            {isDead && <div className="death-overlay">💀</div>}
            {imageLoading && <div className="image-loading-spinner">⏳</div>}
            {!imageLoading && (
              <button
                className="regenerate-image-btn"
                onClick={handleRegenerateImage}
                title="Générer une image"
                disabled={regenerating}
              >
                {regenerating ? '⏳' : '🎨'}
              </button>
            )}
          </div>
        )}
        {isActive && <div className="active-indicator">⚔️</div>}
      </div>

      <div className="character-info">
        <div className="character-name">{personnage.nom}</div>
        <div className="character-level">Niveau {personnage.niveau}</div>
        <div className="character-element-badge">
          <img src={elementIcon} alt={elementName} className="element-icon-small" />
          <span>{elementName}</span>
        </div>
      </div>

      <div className="character-stats-bars">
        <div className="stat-bar-container">
          <div className="stat-bar-label">
            <span>HP</span>
            <span className="stat-value">
              {personnage.hp}/{personnage.hp_max}
            </span>
          </div>
          <div className="hp-bar">
            <div
              className={`hp-fill ${hpPercentage < 30 ? 'low' : hpPercentage < 60 ? 'medium' : 'high'}`}
              style={{ width: `${hpPercentage}%` }}
            />
          </div>
        </div>

        <div className="stat-bar-container">
          <div className="stat-bar-label">
            <span>MP</span>
            <span className="stat-value">
              {personnage.mp}/{personnage.mp_max}
            </span>
          </div>
          <div className="mp-bar">
            <div className="mp-fill" style={{ width: `${mpPercentage}%` }} />
          </div>
        </div>

        {niveaux && xpNeeded > 0 && (
          <div className="stat-bar-container xp-container">
            <div className="stat-bar-label">
              <span>⭐ XP</span>
              <span className="stat-value">
                {currentXpInLevel}/{xpNeeded} XP
              </span>
            </div>
            <div className="xp-bar">
              <div className="xp-fill" style={{ width: `${xpPercentage}%` }} />
            </div>
            <div className="xp-level-info">
              Niveau {personnage.niveau + 1} dans {xpNeeded} XP
            </div>
          </div>
        )}
      </div>

      {showActions && !isDead && (
        <button className="combat-attack-btn" onClick={(e) => {
          e.stopPropagation()
          onAttackClick?.()
        }}>
          Attaquer
        </button>
      )}
    </div>
  )
}

export default CombatCharacter

