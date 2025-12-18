import { useState } from 'react'
import axios from 'axios'
import './Contact.css'

const Contact = () => {
  const [nom, setNom] = useState('')
  const [email, setEmail] = useState('')
  const [message, setMessage] = useState('')
  const [sending, setSending] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setSending(true)

    try {
      const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:5000/api'
      await axios.post(`${API_URL}/contact`, { nom, email, message })
      alert('Message envoyé avec succès!')
      setNom('')
      setEmail('')
      setMessage('')
    } catch (error: any) {
      alert(`Erreur: ${error.response?.data?.error || error.message}`)
    } finally {
      setSending(false)
    }
  }

  return (
    <div className="contact-page">
      <h1>Contact</h1>
      <form className="contact-form" onSubmit={handleSubmit}>
        <div className="form-group">
          <label htmlFor="nom">Nom</label>
          <input
            id="nom"
            type="text"
            value={nom}
            onChange={(e) => setNom(e.target.value)}
            required
          />
        </div>

        <div className="form-group">
          <label htmlFor="email">Email</label>
          <input
            id="email"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
        </div>

        <div className="form-group">
          <label htmlFor="message">Message</label>
          <textarea
            id="message"
            value={message}
            onChange={(e) => setMessage(e.target.value)}
            required
            rows={10}
          />
        </div>

        <button type="submit" disabled={sending} className="submit-button">
          {sending ? 'Envoi...' : 'Envoyer'}
        </button>
      </form>
    </div>
  )
}

export default Contact

