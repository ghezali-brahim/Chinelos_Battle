import { Personnage, Element, Attaque, Niveau } from '../types/models'
import { getElementIcon, getElementName } from '../utils/element'
import { parseAttaques, getXpPercentage, getXpNeededForNextLevel, getCurrentXpInLevel } from '../utils/personnage'
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

  return (
    <div
      className={`personnage-card ${selected ? 'selected' : ''} ${personnage.hp <= 0 ? 'dead' : ''}`}
      onClick={onClick}
    >
      <div className="personnage-header">
        <img src={elementIcon} alt={elementName} className="element-icon" />
        <div className="personnage-info">
          <h4>{personnage.nom}</h4>
          <span className="level">Niveau {personnage.niveau}</span>
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

