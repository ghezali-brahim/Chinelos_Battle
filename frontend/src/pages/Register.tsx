import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import './Auth.css'

const Register = () => {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [username, setUsername] = useState('')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)
  const { signUp } = useAuth()
  const navigate = useNavigate()

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError('')

    // Validation
    if (!/^[a-zA-Z0-9]{4,}$/.test(username)) {
      setError('Le nom d\'utilisateur doit contenir au moins 4 caractères alphanumériques')
      return
    }

    if (!/^[a-zA-Z0-9_$-.*]{4,}$/.test(password)) {
      setError('Le mot de passe doit contenir au moins 4 caractères. Caractères autorisés : alphanumériques, _, $, -, ., *')
      return
    }

    setLoading(true)

    try {
      await signUp(email, password, username)
      navigate('/')
    } catch (err: any) {
      setError(err.message || 'Erreur lors de l\'inscription')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="auth-container">
      <div className="auth-card">
        <h2>Inscription</h2>
        <form onSubmit={handleSubmit}>
          {error && <div className="error-message">{error}</div>}
          <div className="form-group">
            <label htmlFor="username">Nom d'utilisateur</label>
            <input
              type="text"
              id="username"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              required
              minLength={4}
              pattern="[a-zA-Z0-9]{4,}"
              disabled={loading}
            />
          </div>
          <div className="form-group">
            <label htmlFor="email">Email</label>
            <input
              type="email"
              id="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              disabled={loading}
            />
          </div>
          <div className="form-group">
            <label htmlFor="password">Mot de passe</label>
            <input
              type="password"
              id="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              minLength={4}
              disabled={loading}
            />
            <small>Minimum 4 caractères. Caractères autorisés : alphanumériques, _, $, -, ., *</small>
          </div>
          <button type="submit" disabled={loading} className="submit-button">
            {loading ? 'Inscription...' : 'S\'inscrire'}
          </button>
        </form>
        <p className="auth-link">
          Déjà un compte ? <Link to="/login">Connectez-vous</Link>
        </p>
      </div>
    </div>
  )
}

export default Register

