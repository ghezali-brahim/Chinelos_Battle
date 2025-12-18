import './RewardDisplay.css'

interface RewardDisplayProps {
  argent: number
  xpPoints: number
  xpGained?: number
  onClose?: () => void
}

const RewardDisplay = ({ argent, xpPoints, xpGained, onClose }: RewardDisplayProps) => {
  return (
    <div className="reward-display-overlay" onClick={onClose}>
      <div className="reward-display" onClick={(e) => e.stopPropagation()}>
        <h2>Victoire!</h2>
        <div className="reward-content">
          <div className="reward-item">
            <div className="reward-icon">💰</div>
            <div className="reward-info">
              <div className="reward-label">Argent gagné</div>
              <div className="reward-value">+{argent} gils</div>
            </div>
          </div>
          <div className="reward-item">
            <div className="reward-icon">⭐</div>
            <div className="reward-info">
              <div className="reward-label">Expérience gagnée</div>
              <div className="reward-value">+{xpPoints} XP</div>
            </div>
          </div>
        </div>
        {onClose && (
          <button className="reward-close-btn" onClick={onClose}>
            Continuer
          </button>
        )}
      </div>
    </div>
  )
}

export default RewardDisplay

