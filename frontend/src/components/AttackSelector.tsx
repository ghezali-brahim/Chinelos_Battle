import { Attaque } from '../types/models'
import './AttackSelector.css'

interface AttackSelectorProps {
  attacks: Attaque[]
  onSelect: (index: number) => void
  selected: number | null
}

const AttackSelector: React.FC<AttackSelectorProps> = ({ attacks, onSelect, selected }) => {
  return (
    <div className="attack-selector">
      <h4>Sélectionner une attaque:</h4>
      <div className="attack-list">
        {attacks.map((attack, index) => (
          <button
            key={attack.id_attaque}
            className={`attack-option ${selected === index ? 'selected' : ''}`}
            onClick={() => onSelect(index)}
          >
            <span className="attack-name">{attack.nom}</span>
            <span className="attack-details">
              {attack.degats}% dmg - {attack.mp_used} MP
            </span>
          </button>
        ))}
      </div>
    </div>
  )
}

export default AttackSelector

