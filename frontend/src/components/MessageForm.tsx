import { useState } from 'react'
import { useMutation } from '@tanstack/react-query'
import { messagerieService } from '../services/messagerieService'
import './MessageForm.css'

interface Player {
  id_user: string
  username: string
}

interface MessageFormProps {
  players: Player[]
  onSent: () => void
}

const MessageForm: React.FC<MessageFormProps> = ({ players, onSent }) => {
  const [objet, setObjet] = useState('')
  const [contenu, setContenu] = useState('')
  const [destinataire, setDestinataire] = useState('')

  const sendMutation = useMutation({
    mutationFn: messagerieService.sendMessage,
    onSuccess: () => {
      setObjet('')
      setContenu('')
      setDestinataire('')
      onSent()
      alert('Message envoyé avec succès!')
    },
    onError: (error: any) => {
      alert(`Erreur: ${error.response?.data?.error || error.message}`)
    },
  })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    if (!objet || !contenu || !destinataire) {
      alert('Veuillez remplir tous les champs')
      return
    }
    sendMutation.mutate({ objet, contenu, id_destinataire: destinataire })
  }

  return (
    <form className="message-form" onSubmit={handleSubmit}>
      <div className="form-group">
        <label htmlFor="destinataire">Destinataire</label>
        <select
          id="destinataire"
          value={destinataire}
          onChange={(e) => setDestinataire(e.target.value)}
          required
        >
          <option value="">Sélectionner un joueur</option>
          {players.map((player) => (
            <option key={player.id_user} value={player.id_user}>
              {player.username}
            </option>
          ))}
        </select>
      </div>

      <div className="form-group">
        <label htmlFor="objet">Objet</label>
        <input
          id="objet"
          type="text"
          value={objet}
          onChange={(e) => setObjet(e.target.value)}
          required
          maxLength={255}
        />
      </div>

      <div className="form-group">
        <label htmlFor="contenu">Message</label>
        <textarea
          id="contenu"
          value={contenu}
          onChange={(e) => setContenu(e.target.value)}
          required
          rows={10}
        />
      </div>

      <button type="submit" disabled={sendMutation.isPending} className="submit-button">
        {sendMutation.isPending ? 'Envoi...' : 'Envoyer'}
      </button>
    </form>
  )
}

export default MessageForm

