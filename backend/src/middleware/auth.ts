import { Request, Response, NextFunction } from 'express'
import { supabasePublic } from '../config/supabase'

export interface AuthRequest extends Request {
  user?: {
    id: string
    email: string
  }
}

export const authenticate = async (
  req: AuthRequest,
  res: Response,
  next: NextFunction
) => {
  try {
    if (!supabasePublic) {
      return res.status(500).json({ error: 'Supabase not configured' })
    }

    const authHeader = req.headers.authorization
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return res.status(401).json({ error: 'Unauthorized' })
    }

    const token = authHeader.substring(7)
    const { data: { user }, error } = await supabasePublic.auth.getUser(token)

    if (error || !user) {
      return res.status(401).json({ error: 'Invalid token' })
    }

    req.user = {
      id: user.id,
      email: user.email || ''
    }

    next()
  } catch (error) {
    console.error('Auth error:', error)
    return res.status(401).json({ error: 'Authentication failed' })
  }
}

