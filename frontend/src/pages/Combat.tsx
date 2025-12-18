import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import { combatService } from '../services/combatService'
import { apiService } from '../services/api'
import CombatArena from '../components/CombatArena'
import EnemySelector, { EnemyOption } from '../components/EnemySelector'
import './Combat.css'

const Combat = () => {
  const [combatId, setCombatId] = useState<number | null>(null)
  const [selectedEnemyLevel, setSelectedEnemyLevel] = useState<number | null>(null)
  const [isCreating, setIsCreating] = useState(false)

  const { data: elements, isLoading: elementsLoading } = useQuery({
    queryKey: ['elements'],
    queryFn: apiService.getElements,
  })

  const { data: attaques, isLoading: attaquesLoading } = useQuery({
    queryKey: ['attaques'],
    queryFn: apiService.getAttaques,
  })

  const {
    data: enemies,
    isLoading: enemiesLoading,
    error: enemiesError,
    refetch: refetchEnemies,
  } = useQuery({
    queryKey: ['enemies'],
    queryFn: combatService.getEnemiesList,
    enabled: !combatId, // Ne charger que si pas de combat en cours
    retry: 2,
  })

  const handleEnemySelect = async (index: number) => {
    setSelectedEnemyLevel(index)
  }

  const handleStartCombat = async () => {
    if (selectedEnemyLevel === null) {
      alert('Veuillez sélectionner un ennemi')
      return
    }

    setIsCreating(true)
    try {
      // Récupérer le niveau total du joueur pour calculer le niveau de l'ennemi
      const enemiesList = enemies || []
      const selectedEnemy = enemiesList.find((e: EnemyOption) => e.index === selectedEnemyLevel)
      
      if (!selectedEnemy) {
        throw new Error('Ennemi non trouvé')
      }

      const combat = await combatService.createCombat(selectedEnemy.niveauTotal)
      setCombatId(combat.id_combat)
      setSelectedEnemyLevel(null)
    } catch (error: any) {
      console.error('Erreur lors de la création du combat:', error)
      alert(error.response?.data?.error || 'Erreur lors de la création du combat')
    } finally {
      setIsCreating(false)
    }
  }

  const handleCombatEnd = () => {
    setCombatId(null)
    refetchEnemies()
  }

  if (elementsLoading || attaquesLoading) {
    return (
      <div className="combat-page">
        <div className="loading">Chargement...</div>
      </div>
    )
  }

  if (!elements || !attaques) {
    return (
      <div className="combat-page">
        <div className="error">Erreur lors du chargement des données</div>
      </div>
    )
  }

  if (combatId) {
    return (
      <CombatArena
        combatId={combatId}
        elements={elements}
        attaques={attaques}
        onCombatEnd={handleCombatEnd}
      />
    )
  }

  return (
    <div className="combat-page">
      <h1>Combat</h1>
      <p className="combat-intro">
        Sélectionnez un ennemi pour commencer un combat. Les ennemis plus forts offrent de meilleures récompenses mais sont plus difficiles à vaincre.
      </p>
      
      {enemiesError && (
        <div className="error-message">
          Erreur lors du chargement des ennemis: {enemiesError instanceof Error ? enemiesError.message : 'Erreur inconnue'}
          <button onClick={() => refetchEnemies()} className="retry-btn">
            Réessayer
          </button>
        </div>
      )}
      
      <EnemySelector
        enemies={enemies || []}
        onSelect={handleEnemySelect}
        isLoading={enemiesLoading}
      />

      {!enemiesLoading && enemies && enemies.length === 0 && (
        <div className="no-enemies-message">
          <p>Vous n'avez pas encore d'équipe. Allez à la boutique pour acheter votre premier personnage!</p>
          <a href="/boutique" className="go-to-boutique-btn">
            Aller à la boutique
          </a>
        </div>
      )}

      <div className="combat-actions">
        <button
          onClick={handleStartCombat}
          className="start-combat-btn"
          disabled={selectedEnemyLevel === null || isCreating}
        >
          {isCreating ? 'Création du combat...' : 'Démarrer le combat'}
        </button>
      </div>

      {selectedEnemyLevel !== null && (
        <div className="selected-enemy-info">
          Ennemi sélectionné: Niveau {selectedEnemyLevel > 0 ? `+${selectedEnemyLevel}` : selectedEnemyLevel}
        </div>
      )}
    </div>
  )
}

export default Combat
