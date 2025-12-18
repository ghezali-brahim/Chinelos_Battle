import { Message } from '../types/models'
import './MessageList.css'

interface MessageListProps {
  messages: Message[]
  type: 'recus' | 'envoyes'
  selectedMessage: number | null
  onMessageClick: (messageId: number, lu: boolean) => void
}

const MessageList: React.FC<MessageListProps> = ({ messages, type, selectedMessage, onMessageClick }) => {
  if (messages.length === 0) {
    return (
      <div className="messages-empty">
        <p>Aucun message {type === 'recus' ? 'reçu' : 'envoyé'}</p>
      </div>
    )
  }

  const formatDate = (dateString: string) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  }

  return (
    <div className="message-list">
      {messages.map((message) => (
        <div
          key={message.id_message}
          className={`message-item ${selectedMessage === message.id_message ? 'selected' : ''} ${!message.lu && type === 'recus' ? 'unread' : ''}`}
          onClick={() => onMessageClick(message.id_message, message.lu)}
        >
          <div className="message-header">
            <h4>{message.objet}</h4>
            <span className="message-date">{formatDate(message.date_envoie)}</span>
          </div>
          <p className="message-preview">{message.contenu.substring(0, 100)}...</p>
          {!message.lu && type === 'recus' && <span className="unread-badge">Nouveau</span>}
        </div>
      ))}
    </div>
  )
}

export default MessageList

