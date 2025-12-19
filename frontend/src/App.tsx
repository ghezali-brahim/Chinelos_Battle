import { BrowserRouter as Router, Routes, Route } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { AuthProvider } from './context/AuthContext'
import Home from './pages/Home'
import Login from './pages/Login'
import Register from './pages/Register'
import Combat from './pages/Combat'
import Boutique from './pages/Boutique'
import Profile from './pages/Profile'
import Messagerie from './pages/Messagerie'
import Contact from './pages/Contact'
import ProtectedRoute from './components/ProtectedRoute'
import Layout from './components/Layout'

const queryClient = new QueryClient()

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <Router
          future={{
            v7_startTransition: true,
            v7_relativeSplatPath: true,
          }}
        >
          <Layout>
            <Routes>
              <Route path="/" element={<Home />} />
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Register />} />
              <Route
                path="/combat"
                element={
                  <ProtectedRoute>
                    <Combat />
                  </ProtectedRoute>
                }
              />
              <Route
                path="/boutique"
                element={
                  <ProtectedRoute>
                    <Boutique />
                  </ProtectedRoute>
                }
              />
              <Route
                path="/profil"
                element={
                  <ProtectedRoute>
                    <Profile />
                  </ProtectedRoute>
                }
              />
              <Route
                path="/messagerie"
                element={
                  <ProtectedRoute>
                    <Messagerie />
                  </ProtectedRoute>
                }
              />
              <Route path="/contact" element={<Contact />} />
            </Routes>
          </Layout>
        </Router>
      </AuthProvider>
    </QueryClientProvider>
  )
}

export default App

