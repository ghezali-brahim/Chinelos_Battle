import { Equipe } from '../types/models'
import PersonnageCard from './PersonnageCard'
import { useQuery } from '@tanstack/react-query'
import { apiService } from '../services/api'
import './EquipeList.css'

interface EquipeListProps {
  equipes: Equipe[]
}

const EquipeList: React.FC<EquipeListProps> = ({ equipes }) => {
  const { data: elements } = useQuery({
    queryKey: ['elements'],
    queryFn: apiService.getElements,
  })

  const { data: attaques } = useQuery({
    queryKey: ['attaques'],
    queryFn: apiService.getAttaques,
  })

  const { data: niveaux } = useQuery({
    queryKey: ['niveaux'],
    queryFn: apiService.getNiveaux,
  })

  if (!elements || !attaques) {
    return <div>Chargement...</div>
  }

  if (equipes.length === 0) {
    return <div className="no-teams">Aucune équipe pour le moment</div>
  }

  return (
    <div className="equipe-list">
      {equipes.map((equipe) => (
        <div key={equipe.id_equipe} className="equipe-card">
          <h3>Équipe #{equipe.id_equipe}</h3>
          <div className="personnages-grid">
            {equipe.personnages.length === 0 ? (
              <p>Aucun personnage dans cette équipe</p>
            ) : (
              equipe.personnages.map((personnage) => (
                <PersonnageCard
                  key={personnage.id_personnage}
                  personnage={personnage}
                  elements={elements}
                  attaques={attaques}
                  niveaux={niveaux}
                />
              ))
            )}
          </div>
        </div>
      ))}
    </div>
  )
}

export default EquipeList

