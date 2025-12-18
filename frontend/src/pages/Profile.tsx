import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import { joueurService } from '../services/joueurService'
import TeamManager from '../components/TeamManager'
import CharacterStats from '../components/CharacterStats'
import { apiService } from '../services/api'
import { Personnage } from '../types/models'
import './Profile.css'

const Profile = () => {
  const [selectedTab, setSelectedTab] = useState<'profile' | 'teams'>('profile')
  const [selectedPersonnage, setSelectedPersonnage] = useState<Personnage | null>(null)

  const { data: profile, isLoading: profileLoading } = useQuery({
    queryKey: ['profile'],
    queryFn: joueurService.getProfile,
  })

  const { data: stats, isLoading: statsLoading } = useQuery({
    queryKey: ['stats'],
    queryFn: joueurService.getStats,
  })

  const { data: equipeActive } = useQuery({
    queryKey: ['equipe-active'],
    queryFn: joueurService.getEquipeActive,
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

  if (profileLoading || statsLoading) {
    return (
      <div className="profile-page">
        <div className="loading">Chargement du profil...</div>
      </div>
    )
  }

  if (!profile) {
    return (
      <div className="profile-page">
        <div className="error">Erreur lors du chargement du profil</div>
      </div>
    )
  }

  const totalBattles = (profile.nombre_victoire || 0) + (profile.nombre_defaite || 0)
  const winRate = totalBattles > 0 ? ((profile.nombre_victoire || 0) / totalBattles) * 100 : 0

  return (
    <div className="profile-page">
      <div className="profile-tabs">
        <button
          className={selectedTab === 'profile' ? 'active' : ''}
          onClick={() => setSelectedTab('profile')}
        >
          📊 Profil
        </button>
        <button
          className={selectedTab === 'teams' ? 'active' : ''}
          onClick={() => {
            setSelectedTab('teams')
            setSelectedPersonnage(null)
          }}
        >
          ⚔️ Gestion des équipes
        </button>
      </div>

      {selectedTab === 'profile' && (
        <div className="profile-content">
          <div className="profile-card">
            <div className="profile-header">
              <div className="profile-avatar">
                <div className="avatar-circle">{profile.username.charAt(0).toUpperCase()}</div>
              </div>
              <div className="profile-info">
                <h2>{profile.username}</h2>
                <span className="profile-email">{profile.email}</span>
                {stats && (
                  <div className="profile-level">
                    <span>Niveau total: {stats.niveauTotal || 0}</span>
                    {stats.nombrePersonnages && (
                      <span>• {stats.nombrePersonnages} personnage(s)</span>
                    )}
                  </div>
                )}
              </div>
            </div>

            <div className="profile-stats-grid">
              <div className="stat-card money">
                <div className="stat-icon">💰</div>
                <div className="stat-content">
                  <span className="stat-label">Argent</span>
                  <span className="stat-value">{profile.argent || 0} gils</span>
                </div>
              </div>

              <div className="stat-card victories">
                <div className="stat-icon">🏆</div>
                <div className="stat-content">
                  <span className="stat-label">Victoires</span>
                  <span className="stat-value">{profile.nombre_victoire || 0}</span>
                </div>
              </div>

              <div className="stat-card defeats">
                <div className="stat-icon">💔</div>
                <div className="stat-content">
                  <span className="stat-label">Défaites</span>
                  <span className="stat-value">{profile.nombre_defaite || 0}</span>
                </div>
              </div>

              <div className="stat-card winrate">
                <div className="stat-icon">📈</div>
                <div className="stat-content">
                  <span className="stat-label">Taux de victoire</span>
                  <span className="stat-value">{winRate.toFixed(1)}%</span>
                  <span className="stat-sublabel">{totalBattles} combat(s)</span>
                </div>
              </div>
            </div>

            {stats && (
              <div className="profile-advanced-stats">
                <h3>Statistiques avancées</h3>
                <div className="advanced-stats-grid">
                  <div className="advanced-stat">
                    <span className="advanced-stat-label">Niveau maximum</span>
                    <span className="advanced-stat-value">{stats.niveauMax || 0}</span>
                  </div>
                  <div className="advanced-stat">
                    <span className="advanced-stat-label">Niveau total</span>
                    <span className="advanced-stat-value">{stats.niveauTotal || 0}</span>
                  </div>
                  <div className="advanced-stat">
                    <span className="advanced-stat-label">Total personnages</span>
                    <span className="advanced-stat-value">{stats.nombrePersonnages || 0}</span>
                  </div>
                </div>
              </div>
            )}

            {/* Aperçu équipe active */}
            {equipeActive && equipeActive.personnages.length > 0 && (
              <div className="active-team-preview">
                <h3>Équipe active</h3>
                <div className="team-preview-grid">
                  {equipeActive.personnages.slice(0, 6).map((personnage) => (
                    <div
                      key={personnage.id_personnage}
                      className="team-preview-card"
                      onClick={() => {
                        setSelectedPersonnage(personnage)
                        setSelectedTab('teams')
                      }}
                    >
                      <div className="preview-character-name">{personnage.nom}</div>
                      <div className="preview-character-level">Niv. {personnage.niveau}</div>
                      <div className="preview-character-hp">
                        HP: {personnage.hp}/{personnage.hp_max}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        </div>
      )}

      {selectedTab === 'teams' && (
        <div className="teams-content">
          {selectedPersonnage && elements && attaques && (
            <div className="selected-character-panel">
              <button
                className="close-panel-btn"
                onClick={() => setSelectedPersonnage(null)}
              >
                ✕
              </button>
              <CharacterStats
                personnage={selectedPersonnage}
                elements={elements}
                attaques={attaques}
                niveaux={niveaux}
              />
            </div>
          )}

          <TeamManager />
        </div>
      )}
    </div>
  )
}

export default Profile


