import { Item } from '../types/models'
import './ItemCard.css'

interface ItemCardProps {
  item: Item
  onPurchase: (data: any) => void
}

const ItemCard: React.FC<ItemCardProps> = ({ item, onPurchase }) => {
  return (
    <div className="item-card">
      <h3>{item.nom}</h3>
      <p className="item-description">{item.description}</p>
      <div className="item-footer">
        <span className="item-price">{item.prix_achat} gils</span>
        <button
          className="buy-button"
          onClick={() => {
            if (typeof onPurchase === 'function') {
              onPurchase({ type: 'item', item_id: item.id_item })
            }
          }}
        >
          Acheter
        </button>
      </div>
    </div>
  )
}

export default ItemCard

