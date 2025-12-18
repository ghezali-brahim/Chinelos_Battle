import './CombatLog.css'

export interface LogEntry {
  id: string
  type: 'attack' | 'heal' | 'death' | 'victory' | 'defeat'
  attacker?: string
  target?: string
  damage?: number
  elementMultiplier?: number
  message: string
  timestamp: Date
}

interface CombatLogProps {
  logs: LogEntry[]
}

const CombatLog = ({ logs }: CombatLogProps) => {
  return (
    <div className="combat-log">
      <h3>Journal de combat</h3>
      <div className="combat-log-entries">
        {logs.length === 0 ? (
          <div className="combat-log-empty">Aucune action pour le moment</div>
        ) : (
          logs
            .filter((log) => !log.message.toLowerCase().includes('combat not found'))
            .slice(-10)
            .reverse()
            .map((log) => (
            <div key={log.id} className={`combat-log-entry log-${log.type}`}>
              <span className="log-time">
                {log.timestamp.toLocaleTimeString()}
              </span>
              <span className="log-message">{log.message}</span>
              {log.damage !== undefined && (
                <span className="log-damage">-{log.damage} HP</span>
              )}
              {log.elementMultiplier && log.elementMultiplier !== 1.0 && (
                <span className={`log-multiplier ${log.elementMultiplier > 1 ? 'effective' : 'weak'}`}>
                  {log.elementMultiplier > 1 ? 'Efficace!' : 'Pas efficace...'}
                </span>
              )}
            </div>
          ))
        )}
      </div>
    </div>
  )
}

export default CombatLog

