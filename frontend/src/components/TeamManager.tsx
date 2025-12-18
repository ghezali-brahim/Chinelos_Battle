import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { joueurService } from '../services/joueurService'
import { apiService } from '../services/api'
import { Personnage, Element, Attaque } from '../types/models'
import PersonnageCard from './PersonnageCard'
import './TeamManager.css'

const TeamManager = () => {
  const queryClient = useQueryClient()
  const [selectedPersonnage, setSelectedPersonnage] = useState<Personnage | null>(null)

  const { data: equipeActive } = useQuery({
    queryKey: ['equipe-active'],
    queryFn: joueurService.getEquipeActive,
  })

  const { data: equipeReserve } = useQuery({
    queryKey: ['equipe-reserve'],
    queryFn: joueurService.getEquipeReserve,
  })

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

  const transferMutation = useMutation({
    mutationFn: ({ idPersonnage, toActive }: { idPersonnage: number; toActive: boolean }) =>
      joueurService.transferPersonnage(idPersonnage, toActive),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['equipe-active'] })
      queryClient.invalidateQueries({ queryKey: ['equipe-reserve'] })
      queryClient.invalidateQueries({ queryKey: ['equipes'] })
      setSelectedPersonnage(null)
    },
    onError: (error: any) => {
      alert(`Erreur: ${error.response?.data?.error || error.message}`)
    },
  })

  const handleTransfer = (personnage: Personnage, toActive: boolean) => {
    if (toActive && equipeActive?.personnages.length >= 6) {
      alert('L\'équipe active est complète (maximum 6 personnages)')
      return
    }

    transferMutation.mutate({ idPersonnage: personnage.id_personnage, toActive })
  }

  if (!equipeActive || !equipeReserve || !elements || !attaques) {
    return <div className="loading">Chargement des équipes...</div>
  }

  const niveauTotalActive = equipeActive.personnages.reduce((sum, p) => sum + p.niveau, 0)
  const aliveActive = equipeActive.personnages.filter((p) => p.hp > 0)
  const deadActive = equipeActive.personnages.filter((p) => p.hp <= 0)

  const niveauTotalReserve = equipeReserve.personnages.reduce((sum, p) => sum + p.niveau, 0)
  const aliveReserve = equipeReserve.personnages.filter((p) => p.hp > 0)
  const deadReserve = equipeReserve.personnages.filter((p) => p.hp <= 0)

  return (
    <div className="team-manager">
      <div className="team-manager-header">
        <h2>Gestion des équipes</h2>
        <p className="team-manager-info">
          Transférez vos personnages entre l'équipe active (max 6) et la réserve.
          Les personnages morts sont automatiquement transférés en réserve.
        </p>
      </div>

      <div className="teams-container">
        {/* Équipe Active */}
        <div className="team-section active-team">
          <div className="team-header">
            <h3>
              ⚔️ Équipe Active
              <span className="team-badge">
                {equipeActive.personnages.length}/6
              </span>
            </h3>
            <div className="team-stats">
              <span>Niveau total: {niveauTotalActive}</span>
              <span>Vivants: {aliveActive.length}</span>
              {deadActive.length > 0 && (
                <span className="dead-count">Morts: {deadActive.length}</span>
              )}
            </div>
          </div>

          {equipeActive.personnages.length === 0 ? (
            <div className="empty-team">
              <p>Aucun personnage dans l'équipe active</p>
              <p className="empty-team-hint">
                Transférez des personnages depuis la réserve ou achetez-en à la boutique
              </p>
            </div>
          ) : (
            <div className="personnages-list">
              {/* Personnages vivants */}
              {aliveActive.length > 0 && (
                <div className="personnages-group">
                  <h4 className="group-title">Vivants</h4>
                  <div className="personnages-grid">
                    {aliveActive.map((personnage) => (
                      <div key={personnage.id_personnage} className="personnage-wrapper">
                        <PersonnageCard
                          personnage={personnage}
                          elements={elements}
                          attaques={attaques}
                          niveaux={niveaux}
                          selected={selectedPersonnage?.id_personnage === personnage.id_personnage}
                          onClick={() =>
                            setSelectedPersonnage(
                              selectedPersonnage?.id_personnage === personnage.id_personnage
                                ? null
                                : personnage
                            )
                          }
                        />
                        <button
                          className="transfer-btn to-reserve"
                          onClick={() => handleTransfer(personnage, false)}
                          disabled={transferMutation.isPending}
                          title="Transférer vers la réserve"
                        >
                          ↓ Réserve
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Personnages morts */}
              {deadActive.length > 0 && (
                <div className="personnages-group">
                  <h4 className="group-title dead-title">Morts</h4>
                  <div className="personnages-grid">
                    {deadActive.map((personnage) => (
                      <div key={personnage.id_personnage} className="personnage-wrapper">
                        <PersonnageCard
                          personnage={personnage}
                          elements={elements}
                          attaques={attaques}
                          niveaux={niveaux}
                          selected={selectedPersonnage?.id_personnage === personnage.id_personnage}
                          onClick={() =>
                            setSelectedPersonnage(
                              selectedPersonnage?.id_personnage === personnage.id_personnage
                                ? null
                                : personnage
                            )
                          }
                        />
                        <button
                          className="transfer-btn to-reserve"
                          onClick={() => handleTransfer(personnage, false)}
                          disabled={transferMutation.isPending}
                          title="Transférer vers la réserve"
                        >
                          ↓ Réserve
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          )}
        </div>

        {/* Équipe Réserve */}
        <div className="team-section reserve-team">
          <div className="team-header">
            <h3>
              📦 Réserve
              <span className="team-badge">{equipeReserve.personnages.length}</span>
            </h3>
            <div className="team-stats">
              <span>Niveau total: {niveauTotalReserve}</span>
              <span>Vivants: {aliveReserve.length}</span>
              {deadReserve.length > 0 && (
                <span className="dead-count">Morts: {deadReserve.length}</span>
              )}
            </div>
          </div>

          {equipeReserve.personnages.length === 0 ? (
            <div className="empty-team">
              <p>Aucun personnage en réserve</p>
            </div>
          ) : (
            <div className="personnages-list">
              {/* Personnages vivants */}
              {aliveReserve.length > 0 && (
                <div className="personnages-group">
                  <h4 className="group-title">Vivants</h4>
                  <div className="personnages-grid">
                    {aliveReserve.map((personnage) => (
                      <div key={personnage.id_personnage} className="personnage-wrapper">
                        <PersonnageCard
                          personnage={personnage}
                          elements={elements}
                          attaques={attaques}
                          niveaux={niveaux}
                          selected={selectedPersonnage?.id_personnage === personnage.id_personnage}
                          onClick={() =>
                            setSelectedPersonnage(
                              selectedPersonnage?.id_personnage === personnage.id_personnage
                                ? null
                                : personnage
                            )
                          }
                        />
                        <button
                          className="transfer-btn to-active"
                          onClick={() => handleTransfer(personnage, true)}
                          disabled={
                            transferMutation.isPending || equipeActive.personnages.length >= 6
                          }
                          title={
                            equipeActive.personnages.length >= 6
                              ? 'Équipe active complète (max 6)'
                              : 'Ajouter à l\'équipe active'
                          }
                        >
                          ↑ Active
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Personnages morts */}
              {deadReserve.length > 0 && (
                <div className="personnages-group">
                  <h4 className="group-title dead-title">Morts</h4>
                  <div className="personnages-grid">
                    {deadReserve.map((personnage) => (
                      <div key={personnage.id_personnage} className="personnage-wrapper">
                        <PersonnageCard
                          personnage={personnage}
                          elements={elements}
                          attaques={attaques}
                          niveaux={niveaux}
                          selected={selectedPersonnage?.id_personnage === personnage.id_personnage}
                          onClick={() =>
                            setSelectedPersonnage(
                              selectedPersonnage?.id_personnage === personnage.id_personnage
                                ? null
                                : personnage
                            )
                          }
                        />
                        <button
                          className="transfer-btn to-active"
                          onClick={() => handleTransfer(personnage, true)}
                          disabled={
                            transferMutation.isPending || equipeActive.personnages.length >= 6
                          }
                          title={
                            equipeActive.personnages.length >= 6
                              ? 'Équipe active complète (max 6)'
                              : 'Ajouter à l\'équipe active'
                          }
                        >
                          ↑ Active
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          )}
        </div>
      </div>

      {selectedPersonnage && (
        <div className="selected-personnage-info">
          <h4>Personnage sélectionné: {selectedPersonnage.nom}</h4>
          <div className="personnage-details">
            <div className="detail-row">
              <span>Niveau:</span>
              <span>{selectedPersonnage.niveau}</span>
            </div>
            <div className="detail-row">
              <span>HP:</span>
              <span>
                {selectedPersonnage.hp}/{selectedPersonnage.hp_max}
              </span>
            </div>
            <div className="detail-row">
              <span>MP:</span>
              <span>
                {selectedPersonnage.mp}/{selectedPersonnage.mp_max}
              </span>
            </div>
            <div className="detail-row">
              <span>Puissance:</span>
              <span>{selectedPersonnage.puissance}</span>
            </div>
            <div className="detail-row">
              <span>Défense:</span>
              <span>{selectedPersonnage.defense}</span>
            </div>
            <div className="detail-row">
              <span>XP:</span>
              <span>{selectedPersonnage.experience}</span>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

export default TeamManager

