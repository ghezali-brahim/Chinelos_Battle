import { useState, useEffect } from 'react'
import { useQuery } from '@tanstack/react-query'
import { combatService, CombatState, AttackResponse } from '../services/combatService'
import CombatCharacter from './CombatCharacter'
import CombatLog, { LogEntry } from './CombatLog'
import RewardDisplay from './RewardDisplay'
import { Element, Attaque, Personnage, Niveau } from '../types/models'
import { parseAttaques } from '../utils/personnage'
import { apiService } from '../services/api'
import { getAttackIcon, getAttackColor, getAttackBackground, getAttackType } from '../utils/attackUtils'
import './CombatArena.css'

interface CombatArenaProps {
  combatId: number
  elements: Element[]
  attaques: Attaque[]
  onCombatEnd: () => void
}

const CombatArena: React.FC<CombatArenaProps> = ({ combatId, elements, attaques, onCombatEnd }) => {
  const [combatState, setCombatState] = useState<CombatState | null>(null)
  const [logs, setLogs] = useState<LogEntry[]>([])
  const [selectedAttack, setSelectedAttack] = useState<number | null>(null)
  const [selectedEnemy, setSelectedEnemy] = useState<number | null>(null)
  const [rewards, setRewards] = useState<{ argent: number; xpPoints: number; newBalance: number } | null>(null)
  const [isAttacking, setIsAttacking] = useState(false)
  const [attackAnimation, setAttackAnimation] = useState<{ attacker: string; target: string; type: string } | null>(null)

  const { data: combatData, refetch, error: combatError } = useQuery({
    queryKey: ['combat', combatId],
    queryFn: () => combatService.getCombat(combatId),
    enabled: !!combatId && !combatState?.finit,
    refetchInterval: false, // Pas de polling automatique
    retry: false,
  })

  const { data: niveaux } = useQuery({
    queryKey: ['niveaux'],
    queryFn: apiService.getNiveaux,
  })

  useEffect(() => {
    if (combatData) {
      setCombatState(combatData)
    }
  }, [combatData])

  // Raccourcis clavier pour les attaques
  useEffect(() => {
    const handleKeyPress = (e: KeyboardEvent) => {
      // Ignorer si on tape dans un input
      if ((e.target as HTMLElement).tagName === 'INPUT' || (e.target as HTMLElement).tagName === 'TEXTAREA') {
        return
      }

      // Ignorer si on est en train d'attaquer ou si ce n'est pas le tour du joueur
      if (isAttacking || !combatState || combatState.current_turn !== 'joueur' || combatState.finit) {
        return
      }

      // Si une cible est sélectionnée, permettre les raccourcis d'attaque
      if (selectedEnemy !== null) {
        const currentPersonnage = getCurrentPersonnage()
        if (!currentPersonnage) return

        const attaquesIds = parseAttaques(currentPersonnage.attaques)
        const attackIndex = parseInt(e.key) - 1 // 1, 2, 3 deviennent 0, 1, 2

        if (attackIndex >= 0 && attackIndex < attaquesIds.length) {
          const attaqueId = attaquesIds[attackIndex]
          const attaque = attaques.find((a) => a.id_attaque === attaqueId)

          if (attaque && currentPersonnage.mp >= attaque.mp_used) {
            e.preventDefault()
            handleAttack(attackIndex)
          }
        }
      }
    }

    window.addEventListener('keydown', handleKeyPress)
    return () => window.removeEventListener('keydown', handleKeyPress)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [selectedEnemy, combatState, isAttacking, attaques])

  // Gérer les erreurs de combat non trouvé
  useEffect(() => {
    if (combatError) {
      const status = (combatError as any).response?.status
      if (status === 404) {
        // Ne pas ajouter de log, juste rediriger après un délai
        const timeout = setTimeout(() => {
          onCombatEnd()
        }, 1500)
        return () => clearTimeout(timeout)
      }
    }
  }, [combatError, onCombatEnd])

  const addLog = (entry: Omit<LogEntry, 'id' | 'timestamp'>) => {
    const newLog: LogEntry = {
      ...entry,
      id: Date.now().toString() + Math.random().toString(),
      timestamp: new Date(),
    }
    setLogs((prev) => [...prev, newLog])
  }

  const getCurrentPersonnage = (): Personnage | null => {
    if (!combatState) return null
    
    // S'assurer que l'index pointe vers un personnage vivant
    const index = combatState.current_personnage_index_joueur
    const personnage = combatState.joueur_team[index]
    
    // Si le personnage actuel est mort, trouver le premier vivant
    if (!personnage || personnage.hp <= 0) {
      const alivePersonnage = combatState.joueur_team.find(p => p.hp > 0)
      return alivePersonnage || null
    }
    
    return personnage
  }

  const handleAttack = async (attackIndex?: number) => {
    // Si attackIndex est fourni, l'utiliser directement (attaque rapide)
    const attackToUse = attackIndex !== undefined ? attackIndex : selectedAttack
    
    if (attackToUse === null || selectedEnemy === null || !combatState || isAttacking) return

    const currentPersonnage = getCurrentPersonnage()
    if (!currentPersonnage || currentPersonnage.hp <= 0) {
      alert('Ce personnage ne peut pas attaquer')
      return
    }

    const targetEnemy = combatState.enemy_team[selectedEnemy]
    if (!targetEnemy || targetEnemy.hp <= 0) {
      alert('Cette cible est déjà morte')
      return
    }

    // Vérifier que le personnage a assez de MP pour l'attaque
    const attaquesIds = parseAttaques(currentPersonnage.attaques)
    const attaqueId = attaquesIds[attackToUse]
    const attaque = attaques.find((a) => a.id_attaque === attaqueId)
    
    if (attaque && currentPersonnage.mp < attaque.mp_used) {
      alert('Pas assez de MP pour cette attaque')
      return
    }

    setIsAttacking(true)

    // Animation d'attaque
    const attaqueUsed = attaques.find((a) => a.id_attaque === parseAttaques(currentPersonnage.attaques)[attackToUse])
    setAttackAnimation({
      attacker: currentPersonnage.nom,
      target: targetEnemy.nom,
      type: getAttackType(attaqueUsed?.degats || 0, attaqueUsed?.mp_used || 0),
    })

    // Attendre un peu pour l'animation
    await new Promise(resolve => setTimeout(resolve, 300))

    try {
      const response: AttackResponse = await combatService.executeAttack(
        combatId,
        attackToUse,
        selectedEnemy
      )

      // Mettre à jour l'état du combat
      setCombatState(response.combatState)
      
      // Le refetch se fera automatiquement après le timeout

      // Ajouter les logs
      if (response.attackResult.success) {
        addLog({
          type: 'attack',
          attacker: currentPersonnage.nom,
          target: targetEnemy.nom,
          damage: response.attackResult.damage,
          elementMultiplier: response.attackResult.elementMultiplier,
          message: `${currentPersonnage.nom} attaque ${targetEnemy.nom} et inflige ${response.attackResult.damage} dégâts`,
        })

        if (response.attackResult.targetHpAfter <= 0) {
          addLog({
            type: 'death',
            target: targetEnemy.nom,
            message: `${targetEnemy.nom} est vaincu!`,
          })
        }
      } else {
        addLog({
          type: 'attack',
          attacker: currentPersonnage.nom,
          message: response.attackResult.error || 'Attaque échouée',
        })
      }

      // Log de l'attaque de l'IA si présente
      if (response.aiAttackResult) {
        const aiAttacker = response.aiAttackResult.attacker
        const aiTarget = response.aiAttackResult.target

        addLog({
          type: 'attack',
          attacker: aiAttacker.nom,
          target: aiTarget.nom,
          damage: response.aiAttackResult.damage,
          elementMultiplier: response.aiAttackResult.elementMultiplier,
          message: `${aiAttacker.nom} attaque ${aiTarget.nom} et inflige ${response.aiAttackResult.damage} dégâts`,
        })

        if (response.aiAttackResult.targetHpAfter <= 0) {
          addLog({
            type: 'death',
            target: aiTarget.nom,
            message: `${aiTarget.nom} est vaincu!`,
          })
        }
      }

      // Effacer l'animation
      setTimeout(() => {
        setAttackAnimation(null)
      }, 600)

      // Attendre un peu avant de rafraîchir pour l'animation
      setTimeout(() => {
        refetch()
      }, 800)

      // Gérer les récompenses si le combat est terminé
      if (response.combatState.finit) {
        if (response.combatState.winner === 'joueur') {
          addLog({
            type: 'victory',
            message: 'Vous avez remporté le combat!',
          })
          if (response.rewards) {
            setRewards(response.rewards)
          }
        } else {
          addLog({
            type: 'defeat',
            message: 'Vous avez perdu le combat...',
          })
        }
      }

      // Réinitialiser la sélection
      setSelectedAttack(null)
      setSelectedEnemy(null)
    } catch (error: any) {
      console.error('Erreur lors de l\'attaque:', error)
      addLog({
        type: 'attack',
        message: error.response?.data?.error || 'Erreur lors de l\'attaque',
      })
    } finally {
      setIsAttacking(false)
    }
  }

  if (!combatState) {
    return (
      <div className="combat-arena">
        <div className="loading">Chargement du combat...</div>
      </div>
    )
  }

  const currentPersonnage = getCurrentPersonnage()
  const aliveJoueurTeam = combatState.joueur_team.filter((p) => p.hp > 0)
  const aliveEnemyTeam = combatState.enemy_team.filter((p) => p.hp > 0)

  return (
    <div className="combat-arena">
      <div className="combat-header">
        <h2>Combat #{combatId}</h2>
        <div className="combat-turn">
          Tour: {combatState.nombre_tour}
          {combatState.current_turn === 'joueur' && !combatState.finit && (
            <span className="turn-indicator"> - Votre tour</span>
          )}
        </div>
      </div>

      <div className="teams-container">
        <div className="team player-team">
          <div className="team-header-combat">
            <h3>⚔️ Votre équipe</h3>
            <div className="team-stats-combat">
              <span>Vivants: {aliveJoueurTeam.length}/{combatState.joueur_team.length}</span>
            </div>
          </div>
          {aliveJoueurTeam.length === 0 ? (
            <div className="team-status defeated">💀 Équipe vaincue</div>
          ) : (
            <div className="personnages-grid">
              {combatState.joueur_team.map((personnage, index) => {
                const isAttacking = attackAnimation?.attacker === personnage.nom
                return (
                  <div key={personnage.id_personnage} className={isAttacking ? 'combat-character attacking' : ''}>
                    <CombatCharacter
                      personnage={personnage}
                      elements={elements}
                      niveaux={niveaux}
                      isEnemy={false}
                      isActive={index === combatState.current_personnage_index_joueur}
                      isSelected={false}
                      onClick={() => {
                        if (personnage.hp > 0 && combatState.current_turn === 'joueur') {
                          setSelectedAttack(null)
                          setSelectedEnemy(null)
                        }
                      }}
                    />
                  </div>
                )
              })}
            </div>
          )}
        </div>

        <div className="vs-divider">
          <div className="vs">⚔️</div>
          <div className="vs-text">VS</div>
        </div>

        <div className="team enemy-team">
          <div className="team-header-combat">
            <h3>👹 Équipe adverse</h3>
            <div className="team-stats-combat">
              <span>Vivants: {aliveEnemyTeam.length}/{combatState.enemy_team.length}</span>
            </div>
          </div>
          {aliveEnemyTeam.length === 0 ? (
            <div className="team-status defeated">💀 Équipe vaincue</div>
          ) : (
            <div className="personnages-grid">
              {combatState.enemy_team.map((personnage, index) => {
                const isBeingHit = attackAnimation?.target === personnage.nom
                return (
                  <div key={personnage.id_personnage} className={isBeingHit ? 'combat-character being-hit' : ''}>
                    <CombatCharacter
                      personnage={personnage}
                      elements={elements}
                      niveaux={niveaux}
                      isEnemy={true}
                      isActive={false}
                      isSelected={selectedEnemy === index}
                      onClick={() => {
                        if (
                          personnage.hp > 0 &&
                          combatState.current_turn === 'joueur' &&
                          !combatState.finit &&
                          currentPersonnage &&
                          currentPersonnage.hp > 0
                        ) {
                          setSelectedEnemy(index)
                        }
                      }}
                      showActions={selectedEnemy === index}
                      onAttackClick={() => {
                        if (selectedAttack !== null) {
                          handleAttack()
                        }
                      }}
                    />
                  </div>
                )
              })}
            </div>
          )}
        </div>
      </div>

      {!combatState.finit && combatState.current_turn === 'joueur' && aliveJoueurTeam.length > 0 && (
        <div className="combat-actions">
          {currentPersonnage && currentPersonnage.hp > 0 ? (
            <>
              <div className="current-character-info">
                <h4>Personnage actif: {currentPersonnage.nom}</h4>
                {selectedEnemy !== null && (
                  <p>
                    Cible: {combatState.enemy_team[selectedEnemy]?.nom || 'Inconnu'}
                  </p>
                )}
              </div>

              {selectedEnemy === null && (
                <div className="select-target-prompt">
                  Sélectionnez une cible dans l'équipe adverse
                </div>
              )}

              {selectedEnemy !== null && (
                <div className="attack-selection">
                  <h4>Attaques disponibles (cliquez pour attaquer) :</h4>
                  <div className="attacks-list">
                    {parseAttaques(currentPersonnage.attaques).map((attaqueId, index) => {
                      const attaque = attaques.find((a) => a.id_attaque === attaqueId)
                      if (!attaque) return null

                      const canUse = currentPersonnage.mp >= attaque.mp_used
                      const attackType = getAttackType(attaque.degats, attaque.mp_used)
                      const attackIcon = getAttackIcon(attaque.degats, attaque.mp_used)
                      const attackColor = getAttackColor(attaque.degats, attaque.mp_used)

                      return (
                        <button
                          key={attaque.id_attaque}
                          className={`attack-button quick-attack ${attackType} ${selectedAttack === index ? 'selected' : ''} ${
                            !canUse ? 'disabled' : ''
                          }`}
                          onClick={() => {
                            if (canUse) {
                              // Attaque rapide : lance directement l'attaque
                              handleAttack(index)
                            }
                          }}
                          onMouseEnter={() => {
                            if (canUse) {
                              setSelectedAttack(index)
                            }
                          }}
                          disabled={!canUse || isAttacking}
                          style={{
                            borderColor: canUse ? attackColor : undefined,
                            background: canUse && selectedAttack === index ? getAttackBackground(attaque.degats, attaque.mp_used) : undefined,
                          }}
                        >
                          <div className="attack-header">
                            <span className="attack-icon">{attackIcon}</span>
                            <span className="attack-key-hint">{index + 1}</span>
                          </div>
                          <div className="attack-name">{attaque.nom}</div>
                          <div className="attack-details">
                            <span className="damage-badge">{attaque.degats}%</span>
                            <span className="mp-cost">{attaque.mp_used} MP</span>
                          </div>
                        </button>
                      )
                    })}
                  </div>
                  <div className="attack-hint">
                    💡 Astuce : Utilisez les touches <kbd>1</kbd>, <kbd>2</kbd>, <kbd>3</kbd> pour attaquer rapidement
                  </div>
                </div>
              )}

              {selectedAttack !== null && (
                <button
                  onClick={() => handleAttack()}
                  disabled={
                    selectedAttack === null ||
                    selectedEnemy === null ||
                    isAttacking ||
                    combatState.current_turn !== 'joueur'
                  }
                  className="execute-attack-btn"
                >
                  {isAttacking ? '⚔️ Attaque en cours...' : `⚔️ Lancer l'attaque sélectionnée`}
                </button>
              )}
            </>
          ) : (
            <div className="no-active-character">
              <p>Aucun personnage actif disponible. Veuillez attendre...</p>
            </div>
          )}
        </div>
      )}

      {!combatState.finit && combatState.current_turn === 'enemy' && (
        <div className="enemy-turn-indicator">
          <p>⏳ Tour de l'ennemi en cours...</p>
        </div>
      )}

      {combatState.finit && (
        <div className="combat-end">
          <h2>
            {combatState.winner === 'joueur' ? 'Victoire!' : 'Défaite...'}
          </h2>
          {combatState.winner === 'joueur' && (
            <button className="continue-btn" onClick={onCombatEnd}>
              Continuer
            </button>
          )}
          {combatState.winner === 'enemy' && (
            <button className="continue-btn" onClick={onCombatEnd}>
              Retour
            </button>
          )}
        </div>
      )}

      <CombatLog logs={logs} />

      {rewards && (
        <RewardDisplay
          argent={rewards.argent}
          xpPoints={rewards.xpPoints}
          onClose={() => {
            setRewards(null)
            onCombatEnd()
          }}
        />
      )}
    </div>
  )
}

export default CombatArena
