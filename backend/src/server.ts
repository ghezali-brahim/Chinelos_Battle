import express, { Request, Response, NextFunction } from 'express'
import cors from 'cors'
import 'express-async-errors'
import 'dotenv/config'
import { checkSupabaseConfig } from './config/supabase'
import authRoutes from './routes/auth'
import combatRoutes from './routes/combat'
import boutiqueRoutes from './routes/boutique'
import joueurRoutes from './routes/joueur'
import messagerieRoutes from './routes/messagerie'
import contactRoutes from './routes/contact'
import gameRoutes from './routes/game'

const app = express()
const PORT = process.env.PORT || 5000

// Vérifier la configuration Supabase
checkSupabaseConfig()

// Middleware
app.use(cors({
  origin: process.env.FRONTEND_URL || 'http://localhost:3000',
  credentials: true
}))
app.use(express.json())
app.use(express.urlencoded({ extended: true }))

// Routes
app.use('/api/auth', authRoutes)
app.use('/api/combat', combatRoutes)
app.use('/api/boutique', boutiqueRoutes)
app.use('/api/joueur', joueurRoutes)
app.use('/api/messagerie', messagerieRoutes)
app.use('/api/contact', contactRoutes)
app.use('/api', gameRoutes)

// Health check
app.get('/health', (req: Request, res: Response) => {
  res.json({ status: 'OK', timestamp: new Date().toISOString() })
})

// Error handling middleware
app.use((err: Error, req: Request, res: Response, next: NextFunction) => {
  console.error('Error:', err)
  res.status(500).json({
    error: 'Internal server error',
    message: process.env.NODE_ENV === 'development' ? err.message : undefined
  })
})

// 404 handler
app.use((req: Request, res: Response) => {
  res.status(404).json({ error: 'Route not found' })
})

// Démarrer le serveur avec gestion d'erreur pour le port
const server = app.listen(PORT, () => {
  console.log(`✅ Server running on port ${PORT}`)
  console.log(`Environment: ${process.env.NODE_ENV || 'development'}`)
  console.log(`Frontend URL: ${process.env.FRONTEND_URL || 'http://localhost:3000'}`)
}).on('error', (err: NodeJS.ErrnoException) => {
  if (err.code === 'EADDRINUSE') {
    console.error(`❌ Port ${PORT} is already in use. Please stop the process using this port or change PORT in .env`)
    console.error(`   You can find the process with: lsof -ti:${PORT}`)
    process.exit(1)
  } else {
    throw err
  }
})

export default app

