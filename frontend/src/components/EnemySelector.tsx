import { useState } from 'react'
import './EnemySelector.css'

export interface EnemyOption {
  index: number
  niveauTotal: number
  nombrePersonnages: number
}

interface EnemySelectorProps {
  enemies: EnemyOption[]
  onSelect: (index: number) => void
  isLoading?: boolean
}

const EnemySelector = ({ enemies, onSelect, isLoading }: EnemySelectorProps) => {
  const [selectedIndex, setSelectedIndex] = useState<number | null>(null)

  const handleSelect = (index: number) => {
    setSelectedIndex(index)
    onSelect(index)
  }

  if (isLoading) {
    return <div className="enemy-selector-loading">Chargement des ennemis...</div>
  }

  if (enemies.length === 0) {
    return <div className="enemy-selector-empty">Aucun ennemi disponible</div>
  }

  return (
    <div className="enemy-selector">
      <h3>Sélectionner un ennemi</h3>
      <p className="enemy-selector-info">
        Choisissez un ennemi de niveau différent pour varier la difficulté
      </p>
      <div className="enemy-list">
        {enemies.map((enemy) => {
          const levelDiff = enemy.index > 0 ? `+${enemy.index}` : enemy.index.toString()
          const difficulty =
            enemy.index < -1
              ? 'très facile'
              : enemy.index === -1
              ? 'facile'
              : enemy.index === 0
              ? 'normal'
              : enemy.index <= 2
              ? 'difficile'
              : enemy.index <= 5
              ? 'très difficile'
              : 'extrême'

          return (
            <div
              key={enemy.index}
              className={`enemy-card ${selectedIndex === enemy.index ? 'selected' : ''}`}
              onClick={() => handleSelect(enemy.index)}
            >
              <div className="enemy-card-header">
                <span className="enemy-level-diff">Niveau {levelDiff}</span>
                <span className={`enemy-difficulty difficulty-${difficulty.replace(' ', '-')}`}>
                  {difficulty}
                </span>
              </div>
              <div className="enemy-card-body">
                <div className="enemy-stat">
                  <span className="stat-label">Niveau total:</span>
                  <span className="stat-value">{enemy.niveauTotal}</span>
                </div>
                <div className="enemy-stat">
                  <span className="stat-label">Personnages:</span>
                  <span className="stat-value">{enemy.nombrePersonnages}</span>
                </div>
              </div>
            </div>
          )
        })}
      </div>
    </div>
  )
}

export default EnemySelector

