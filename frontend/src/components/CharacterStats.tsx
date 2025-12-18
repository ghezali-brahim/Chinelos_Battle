import { Personnage, Element, Attaque, Niveau } from '../types/models'
import { getElementName, getElementIcon } from '../utils/element'
import { parseAttaques, getXpPercentage, getXpNeededForNextLevel } from '../utils/personnage'
import './CharacterStats.css'

interface CharacterStatsProps {
  personnage: Personnage
  elements: Element[]
  attaques: Attaque[]
  niveaux?: Niveau[]
}

const CharacterStats = ({ personnage, elements, attaques, niveaux }: CharacterStatsProps) => {
  const elementName = getElementName(elements, personnage.element)
  const elementIcon = getElementIcon(personnage.element)
  const attaquesIds = parseAttaques(personnage.attaques)
  const personnageAttaques = attaquesIds
    .map((id) => attaques.find((a) => a.id_attaque === id))
    .filter(Boolean) as Attaque[]

  const hpPercentage = (personnage.hp / personnage.hp_max) * 100
  const mpPercentage = (personnage.mp / personnage.mp_max) * 100

  const xpPercentage = niveaux ? getXpPercentage(niveaux, personnage) : 0
  const xpNeeded = niveaux ? getXpNeededForNextLevel(niveaux, personnage) : 0

  const isDead = personnage.hp <= 0

  return (
    <div className={`character-stats ${isDead ? 'dead' : ''}`}>
      <div className="character-header">
        <div className="character-title">
          <img src={elementIcon} alt={elementName} className="element-icon-large" />
          <div>
            <h3>{personnage.nom}</h3>
            <span className="character-element">{elementName}</span>
          </div>
        </div>
        <div className="character-level">
          <span className="level-badge">Niveau {personnage.niveau}</span>
        </div>
      </div>

      {/* Barres HP/MP */}
      <div className="stat-bars">
        <div className="stat-bar">
          <div className="stat-bar-label">
            <span>HP</span>
            <span className="stat-bar-values">
              {personnage.hp} / {personnage.hp_max}
            </span>
          </div>
          <div className="progress-bar">
            <div
              className={`progress-fill hp ${isDead ? 'empty' : ''}`}
              style={{ width: `${hpPercentage}%` }}
            />
          </div>
        </div>

        <div className="stat-bar">
          <div className="stat-bar-label">
            <span>MP</span>
            <span className="stat-bar-values">
              {personnage.mp} / {personnage.mp_max}
            </span>
          </div>
          <div className="progress-bar">
            <div
              className="progress-fill mp"
              style={{ width: `${mpPercentage}%` }}
            />
          </div>
        </div>

        {niveaux && xpNeeded > 0 && (
          <div className="stat-bar">
            <div className="stat-bar-label">
              <span>XP</span>
              <span className="stat-bar-values">
                {xpPercentage.toFixed(1)}% (Niveau {personnage.niveau + 1} dans {xpNeeded} XP)
              </span>
            </div>
            <div className="progress-bar">
              <div className="progress-fill xp" style={{ width: `${xpPercentage}%` }} />
            </div>
          </div>
        )}
      </div>

      {/* Stats de combat */}
      <div className="combat-stats">
        <div className="combat-stat-item">
          <span className="stat-icon">⚔️</span>
          <div className="stat-content">
            <span className="stat-label">Puissance</span>
            <span className="stat-value">{personnage.puissance}</span>
          </div>
        </div>
        <div className="combat-stat-item">
          <span className="stat-icon">🛡️</span>
          <div className="stat-content">
            <span className="stat-label">Défense</span>
            <span className="stat-value">{personnage.defense}</span>
          </div>
        </div>
      </div>

      {/* Attaques */}
      {personnageAttaques.length > 0 && (
        <div className="attacks-section">
          <h4>Attaques disponibles</h4>
          <div className="attacks-list">
            {personnageAttaques.map((attaque) => (
              <div
                key={attaque.id_attaque}
                className={`attack-item ${personnage.mp < attaque.mp_used ? 'disabled' : ''}`}
              >
                <span className="attack-name">{attaque.nom}</span>
                <div className="attack-details">
                  <span className="attack-damage">💥 {attaque.degats}%</span>
                  <span className="attack-cost">✨ {attaque.mp_used} MP</span>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {isDead && (
        <div className="death-notice">
          ⚠️ Ce personnage est mort. Soignez-le à la boutique pour le récupérer.
        </div>
      )}
    </div>
  )
}

export default CharacterStats

