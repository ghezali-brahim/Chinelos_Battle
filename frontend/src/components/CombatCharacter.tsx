import { Personnage, Element, Niveau } from '../types/models'
import { getElementIcon, getElementName } from '../utils/element'
import { getCharacterImageWithFallback, getCharacterAvatarStyle } from '../utils/characterImage'
import { getXpPercentage, getXpNeededForNextLevel, getCurrentXpInLevel } from '../utils/personnage'
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

  const characterImage = getCharacterImageWithFallback(personnage.element, personnage.niveau, isEnemy)

  return (
    <div
      className={`combat-character ${isEnemy ? 'enemy' : 'ally'} ${isActive ? 'active' : ''} ${isSelected ? 'selected' : ''} ${isDead ? 'dead' : ''}`}
      onClick={onClick}
    >
      <div className="character-image-wrapper">
        <div
          className="character-avatar"
          style={getCharacterAvatarStyle(personnage.element)}
        >
          {!isDead && (
            <img
              src={elementIcon}
              alt={elementName}
              className="element-overlay"
            />
          )}
          {isDead && <div className="death-overlay">💀</div>}
        </div>
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

