# Chinelos Battle - React.js + Supabase

Migration complète du projet PHP vers React.js avec Supabase.

## Structure du projet

```
frontend/          # Application React.js (Vite + TypeScript)
backend/           # API REST Node.js/Express
supabase/          # Configuration Supabase (migrations SQL, auth, storage)
```

## Installation rapide

### 1. Installer toutes les dépendances

```bash
npm run install:all
```

Cette commande installe les dépendances à la racine, dans `frontend/` et dans `backend/`.

### 2. Configuration Supabase

1. Créer un projet sur [supabase.com](https://supabase.com)
2. Récupérer les clés API depuis les paramètres du projet
3. Appliquer les migrations SQL :
   - Via l'interface Supabase : SQL Editor > New Query
   - Copier le contenu de `supabase/migrations/001_initial_schema.sql`
   - Exécuter la requête

### 3. Configurer les variables d'environnement

**Frontend :**
```bash
cd frontend
cp .env.example .env
# Modifier .env avec vos clés Supabase
```

**Backend :**
```bash
cd backend
cp .env.example .env
# Modifier .env avec vos clés Supabase
```

### 4. Lancer l'application

**Mode développement (recommandé) :**
```bash
npm run dev
```

Cette commande lance simultanément :
- Frontend sur `http://localhost:3000`
- Backend sur `http://localhost:5000`

**Mode production :**
```bash
npm run build    # Compile les deux projets
npm start        # Lance les versions compilées
```

## Commandes disponibles

À la racine du projet :

- `npm run install:all` - Installe toutes les dépendances (racine, frontend, backend)
- `npm run dev` - Lance frontend et backend en mode développement
- `npm run dev:frontend` - Lance uniquement le frontend
- `npm run dev:backend` - Lance uniquement le backend
- `npm run build` - Compile frontend et backend pour la production
- `npm run build:frontend` - Compile uniquement le frontend
- `npm run build:backend` - Compile uniquement le backend
- `npm start` - Lance les versions compilées en production
- `npm run lint` - Vérifie le code des deux projets

## Variables d'environnement

### Frontend (.env)
```
VITE_SUPABASE_URL=https://your-project.supabase.co
VITE_SUPABASE_ANON_KEY=your-anon-key
VITE_API_URL=http://localhost:5000/api
```

### Backend (.env)
```
PORT=5000
NODE_ENV=development
FRONTEND_URL=http://localhost:3000
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-anon-key
SUPABASE_SERVICE_ROLE_KEY=your-service-role-key
```

## Fonctionnalités

- ✅ Authentification (Supabase Auth)
- ✅ Page d'accueil avec classement
- ✅ Module Combat (structure de base)
- ✅ Module Boutique (achat personnages, soins, items)
- ✅ Module Profil (affichage équipes et statistiques)
- ✅ Module Messagerie
- ✅ Module Contact

## Technologies utilisées

- **Frontend**: React 18, TypeScript, Vite, React Router, React Query, Zustand
- **Backend**: Node.js, Express, TypeScript
- **Base de données**: Supabase (PostgreSQL)
- **Authentification**: Supabase Auth
- **Realtime**: Supabase Realtime (pour les combats et messages)

## Déploiement

### Frontend (Vercel/Netlify)
```bash
npm run build:frontend
# Déployer le dossier frontend/dist/
```

### Backend (Railway/Render/Heroku)
```bash
npm run build:backend
# Déployer avec les variables d'environnement configurées
```

## Notes

- Les images doivent être uploadées dans Supabase Storage (buckets: `avatars`, `elements`, `images`)
- La logique de combat complète nécessite encore des ajustements backend
- Le système d'IA pour les combats doit être implémenté dans le backend
