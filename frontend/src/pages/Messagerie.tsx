import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { messagerieService } from '../services/messagerieService'
import { joueurService } from '../services/joueurService'
import MessageList from '../components/MessageList'
import MessageForm from '../components/MessageForm'
import './Messagerie.css'

const Messagerie = () => {
  const [activeTab, setActiveTab] = useState<'recus' | 'envoyes' | 'composer'>('recus')
  const [selectedMessage, setSelectedMessage] = useState<number | null>(null)

  const queryClient = useQueryClient()

  const { data: messages } = useQuery({
    queryKey: ['messages'],
    queryFn: messagerieService.getMessages,
  })

  const { data: leaderboard } = useQuery({
    queryKey: ['leaderboard'],
    queryFn: joueurService.getLeaderboard,
    enabled: activeTab === 'composer',
  })

  const markAsReadMutation = useMutation({
    mutationFn: messagerieService.markAsRead,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['messages'] })
    },
  })

  const handleMessageClick = (messageId: number, lu: boolean) => {
    setSelectedMessage(messageId)
    if (!lu) {
      markAsReadMutation.mutate(messageId)
    }
  }

  return (
    <div className="messagerie-page">
      <h1>Messagerie</h1>

      <div className="messagerie-tabs">
        <button
          className={activeTab === 'recus' ? 'active' : ''}
          onClick={() => setActiveTab('recus')}
        >
          Reçus ({messages?.recus.filter(m => !m.lu).length || 0})
        </button>
        <button
          className={activeTab === 'envoyes' ? 'active' : ''}
          onClick={() => setActiveTab('envoyes')}
        >
          Envoyés
        </button>
        <button
          className={activeTab === 'composer' ? 'active' : ''}
          onClick={() => setActiveTab('composer')}
        >
          Composer
        </button>
      </div>

      {activeTab === 'recus' && (
        <MessageList
          messages={messages?.recus || []}
          type="recus"
          selectedMessage={selectedMessage}
          onMessageClick={handleMessageClick}
        />
      )}

      {activeTab === 'envoyes' && (
        <MessageList
          messages={messages?.envoyes || []}
          type="envoyes"
          selectedMessage={selectedMessage}
          onMessageClick={handleMessageClick}
        />
      )}

      {activeTab === 'composer' && (
        <MessageForm
          players={leaderboard || []}
          onSent={() => {
            setActiveTab('envoyes')
            queryClient.invalidateQueries({ queryKey: ['messages'] })
          }}
        />
      )}
    </div>
  )
}

export default Messagerie

