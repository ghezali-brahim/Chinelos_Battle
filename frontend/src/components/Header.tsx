import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import './Header.css'

const Header = () => {
  const { user, signOut } = useAuth()
  const navigate = useNavigate()

  const handleLogout = async () => {
    try {
      await signOut()
      navigate('/')
    } catch (error) {
      console.error('Erreur lors de la déconnexion:', error)
    }
  }

  return (
    <header className="header">
      <div className="block-header">
        <div className="logo">
          <Link to="/">
            <img src="/logo.png" alt="Chinelos Battle" width="175" height="97" />
          </Link>
        </div>
        <nav className="menu">
          <ul>
            <li>
              <Link to="/">Accueil</Link>
            </li>
            {user && (
              <>
                <li>
                  <Link to="/combat">Combat</Link>
                </li>
                <li>
                  <Link to="/boutique">Boutique</Link>
                </li>
                <li>
                  <Link to="/profil">Joueur</Link>
                </li>
                <li>
                  <Link to="/messagerie">Messagerie</Link>
                </li>
              </>
            )}
            <li>
              <Link to="/contact">Contact</Link>
            </li>
          </ul>
        </nav>
        <div className="auth-section">
          {user ? (
            <div className="user-info">
              <span>{user.email}</span>
              <button onClick={handleLogout}>Déconnexion</button>
            </div>
          ) : (
            <div className="auth-buttons">
              <Link to="/login">Connexion</Link>
              <Link to="/register">Inscription</Link>
            </div>
          )}
        </div>
      </div>
    </header>
  )
}

export default Header

