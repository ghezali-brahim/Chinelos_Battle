import { useState, useEffect } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { boutiqueService } from '../services/boutiqueService'
import { joueurService } from '../services/joueurService'
import ItemCard from '../components/ItemCard'
import Inventory from '../components/Inventory'
import { generateCharacterName, generateMultipleNames } from '../utils/nameGenerator'
import './Boutique.css'

const Boutique = () => {
  const [activeTab, setActiveTab] = useState<'shop' | 'inventory'>('shop')
  const [purchaseForm, setPurchaseForm] = useState<{
    type: 'personnage' | 'soin' | null
    nom_personnage?: string
    id_element?: number
  }>({ type: null })
  const [suggestedNames, setSuggestedNames] = useState<string[]>([])

  const queryClient = useQueryClient()

  const { data: items, isLoading: itemsLoading } = useQuery({
    queryKey: ['boutique-items'],
    queryFn: boutiqueService.getItems,
  })

  const { data: inventory } = useQuery({
    queryKey: ['inventory'],
    queryFn: boutiqueService.getInventory,
    enabled: activeTab === 'inventory',
  })

  const { data: profile } = useQuery({
    queryKey: ['profile'],
    queryFn: joueurService.getProfile,
  })

  const purchaseMutation = useMutation({
    mutationFn: boutiqueService.buyItem,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['profile'] })
      queryClient.invalidateQueries({ queryKey: ['inventory'] })
      setPurchaseForm({ type: null })
      alert('Achat effectué avec succès!')
    },
    onError: (error: any) => {
      alert(`Erreur: ${error.response?.data?.error || error.message}`)
    },
  })

  const handlePurchase = async () => {
    if (!purchaseForm.type) return

    if (purchaseForm.type === 'personnage') {
      if (!purchaseForm.nom_personnage || !purchaseForm.id_element) {
        alert('Veuillez remplir tous les champs')
        return
      }
      
      // Valider et nettoyer le nom
      const cleanedName = purchaseForm.nom_personnage.trim().replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '')
      if (cleanedName.length < 4) {
        alert('Le nom doit contenir au moins 4 caractères alphanumériques')
        return
      }
      
      await purchaseMutation.mutateAsync({
        type: purchaseForm.type,
        nom_personnage: cleanedName,
        id_element: purchaseForm.id_element,
      })
    } else {
      await purchaseMutation.mutateAsync({
        type: purchaseForm.type,
      })
    }
  }

  return (
    <div className="boutique-page">
      <h1>Boutique</h1>

      {profile && (
        <div className="profile-summary">
          <p>Argent: <strong>{profile.argent} gils</strong></p>
        </div>
      )}

      <div className="boutique-tabs">
        <button
          className={activeTab === 'shop' ? 'active' : ''}
          onClick={() => setActiveTab('shop')}
        >
          Magasin
        </button>
        <button
          className={activeTab === 'inventory' ? 'active' : ''}
          onClick={() => setActiveTab('inventory')}
        >
          Inventaire
        </button>
      </div>

      {activeTab === 'shop' && (
        <div className="shop-section">
          <div className="purchase-options">
            <h2>Acheter un personnage</h2>
            <button
              onClick={() => {
                const names = generateMultipleNames(5, 'random')
                setSuggestedNames(names)
                setPurchaseForm({ 
                  type: 'personnage', 
                  nom_personnage: names[0] || generateCharacterName(),
                  id_element: 1 
                })
              }}
              className="purchase-btn"
            >
              Acheter un personnage (5 gils)
            </button>

            {purchaseForm.type === 'personnage' && (
              <div className="purchase-form">
                <div className="name-input-group">
                  <label htmlFor="personnage-name">Nom du personnage</label>
                  <div className="name-input-with-suggestions">
                    <input
                      id="personnage-name"
                      type="text"
                      placeholder="Nom du personnage (min 4 caractères)"
                      value={purchaseForm.nom_personnage || ''}
                      onChange={(e) => setPurchaseForm({ ...purchaseForm, nom_personnage: e.target.value })}
                      minLength={4}
                      pattern="[a-zA-Z0-9_]{4,}"
                    />
                    <button
                      type="button"
                      className="generate-name-btn"
                      onClick={() => {
                        const newName = generateCharacterName()
                        setPurchaseForm({ ...purchaseForm, nom_personnage: newName })
                      }}
                      title="Générer un nouveau nom"
                    >
                      🎲
                    </button>
                  </div>
                  
                  {suggestedNames.length > 0 && (
                    <div className="suggested-names">
                      <span className="suggested-names-label">Noms suggérés:</span>
                      <div className="suggested-names-list">
                        {suggestedNames.map((name, index) => (
                          <button
                            key={index}
                            type="button"
                            className="suggested-name-btn"
                            onClick={() => setPurchaseForm({ ...purchaseForm, nom_personnage: name })}
                          >
                            {name}
                          </button>
                        ))}
                        <button
                          type="button"
                          className="suggested-name-btn refresh-btn"
                          onClick={() => {
                            const newNames = generateMultipleNames(5, 'random')
                            setSuggestedNames(newNames)
                            setPurchaseForm({ ...purchaseForm, nom_personnage: newNames[0] })
                          }}
                          title="Générer d'autres noms"
                        >
                          🔄 Autres noms
                        </button>
                      </div>
                    </div>
                  )}
                </div>

                <div className="element-selection">
                  <label htmlFor="personnage-element">Élément</label>
                  <select
                    id="personnage-element"
                    value={purchaseForm.id_element || 1}
                    onChange={(e) => setPurchaseForm({ ...purchaseForm, id_element: parseInt(e.target.value) })}
                  >
                    <option value={1}>Normal</option>
                    <option value={2}>Feu</option>
                    <option value={3}>Eau</option>
                    <option value={4}>Plante</option>
                  </select>
                </div>

                <div className="purchase-form-actions">
                  <button 
                    onClick={handlePurchase} 
                    disabled={purchaseMutation.isPending}
                    className="confirm-btn"
                  >
                    {purchaseMutation.isPending ? 'Achat en cours...' : 'Confirmer l\'achat (5 gils)'}
                  </button>
                  <button 
                    onClick={() => {
                      setPurchaseForm({ type: null })
                      setSuggestedNames([])
                    }}
                    className="cancel-btn"
                  >
                    Annuler
                  </button>
                </div>
              </div>
            )}

            <h2>Soins</h2>
            <button
              onClick={async () => {
                try {
                  await purchaseMutation.mutateAsync({ type: 'soin' })
                } catch (error) {
                  // L'erreur est déjà gérée dans onError
                }
              }}
              className="purchase-btn"
              disabled={purchaseMutation.isPending}
            >
              Soigner tous les personnages (1 gils)
            </button>
          </div>

          <div className="items-section">
            <h2>Items disponibles</h2>
            {itemsLoading ? (
              <div>Chargement...</div>
            ) : (
              <div className="items-grid">
                {items?.map((item) => (
                  <ItemCard
                    key={item.id_item}
                    item={item}
                    onPurchase={async (data: any) => {
                      await purchaseMutation.mutateAsync(data)
                    }}
                  />
                ))}
              </div>
            )}
          </div>
        </div>
      )}

      {activeTab === 'inventory' && <Inventory inventory={inventory || []} />}
    </div>
  )
}

export default Boutique

