import './Leaderboard.css'

interface Player {
  id_user: number
  username: string
  niveauTotal: number
  connected: boolean
}

interface LeaderboardProps {
  players: Player[]
  loading: boolean
}

const Leaderboard: React.FC<LeaderboardProps> = ({ players, loading }) => {
  if (loading) {
    return <div>Chargement du classement...</div>
  }

  return (
    <div className="leaderboard">
      <h2>Classement des Joueurs</h2>
      <table>
        <thead>
          <tr>
            <th>Rang</th>
            <th>Nom joueur</th>
            <th>Niveau Total</th>
            <th>Connecté</th>
          </tr>
        </thead>
        <tbody>
          {players.length === 0 ? (
            <tr>
              <td colSpan={4} style={{ textAlign: 'center', padding: '20px' }}>
                Aucun joueur pour le moment
              </td>
            </tr>
          ) : (
            players.map((player, index) => (
              <tr key={player.id_user}>
                <td>{index + 1}</td>
                <td>{player.username}</td>
                <td>{player.niveauTotal}</td>
                <td>{player.connected ? 'O' : 'X'}</td>
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  )
}

export default Leaderboard

