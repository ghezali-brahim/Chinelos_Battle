import { InventaireItem } from '../types/models'
import './Inventory.css'

interface InventoryProps {
  inventory: InventaireItem[]
}

const Inventory: React.FC<InventoryProps> = ({ inventory }) => {
  if (inventory.length === 0) {
    return (
      <div className="inventory-empty">
        <p>Votre inventaire est vide</p>
      </div>
    )
  }

  return (
    <div className="inventory">
      <div className="inventory-grid">
        {inventory.map((invItem) => (
          <div key={`${invItem.id_user}-${invItem.id_item}`} className="inventory-item">
            <h4>{invItem.item.nom}</h4>
            <p>{invItem.item.description}</p>
            <span className="quantity">Quantité: {invItem.quantite}</span>
          </div>
        ))}
      </div>
    </div>
  )
}

export default Inventory

