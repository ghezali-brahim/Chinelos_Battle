import { useQuery } from '@tanstack/react-query'
import { joueurService } from '../services/joueurService'
import Leaderboard from '../components/Leaderboard'
import './Home.css'

const Home = () => {
  const { data: players, isLoading: loading } = useQuery({
    queryKey: ['leaderboard'],
    queryFn: joueurService.getLeaderboard,
  })

  return (
    <div className="home">
      <div className="hero">
        <h1>Bienvenue sur Chinelos Battle</h1>
        <p>Un mini-jeu de combat simple et multijoueur</p>
      </div>

      <div className="home-content">
        <div className="left-column">
          <h2>Bienvenue sur notre site !</h2>
          <p>Vous devez vous inscrire pour accéder à notre mini-jeu.</p>
          <p>
            Notre site repasse l'actualité des jeux vidéos pour vous informer des dernières sorties.
          </p>
          <p>
            Nous vous communiquerons les changements qui nous semblent nécessaires pour le jeu via les News et Events.
          </p>
        </div>

        <div className="right-column">
          <Leaderboard players={players} loading={loading} />
        </div>
      </div>
    </div>
  )
}

export default Home

