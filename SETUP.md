# Guide de configuration et démarrage

## ✅ Corrections effectuées

### 1. Gestion du port 5000
- Le serveur détecte maintenant si le port 5000 est déjà utilisé
- Message d'erreur clair avec instruction pour libérer le port

### 2. Configuration Supabase
- Le serveur démarre même si Supabase n'est pas encore configuré
- Message d'avertissement au lieu d'un crash
- Toutes les routes vérifient la configuration avant utilisation

### 3. Gestion des erreurs
- Meilleure gestion des erreurs dans toutes les routes
- Messages d'erreur explicites

## 🚀 Démarrage rapide

### 1. Installation des dépendances (première fois)
```bash
npm run install:all
```

### 2. Configuration des variables d'environnement

**Frontend** (`frontend/.env`):
```env
VITE_SUPABASE_URL=https://votre-projet.supabase.co
VITE_SUPABASE_ANON_KEY=votre-clé-anon
VITE_API_URL=http://localhost:5000/api
```

**Backend** (`backend/.env`):
```env
PORT=5000
NODE_ENV=development
FRONTEND_URL=http://localhost:3000
SUPABASE_URL=https://votre-projet.supabase.co
SUPABASE_ANON_KEY=votre-clé-anon
SUPABASE_SERVICE_ROLE_KEY=votre-service-role-key
```

### 3. Libérer le port 5000 si nécessaire
```bash
# Trouver le processus utilisant le port 5000
lsof -ti:5000

# Tuer le processus (remplacez PID par le numéro trouvé)
kill -9 PID

# Ou en une commande
lsof -ti:5000 | xargs kill -9
```

### 4. Lancer l'application
```bash
npm start
```

Cette commande lance:
- ✅ Frontend sur http://localhost:3000
- ✅ Backend sur http://localhost:5000

## 🔧 Résolution de problèmes

### Port 5000 déjà utilisé
```bash
# Trouver et tuer le processus
lsof -ti:5000 | xargs kill -9
```

### Variables d'environnement manquantes
Le serveur démarre mais affichera des avertissements. Configurez vos `.env` fichiers comme indiqué ci-dessus.

### Erreurs de connexion Supabase
1. Vérifiez que vos clés Supabase sont correctes
2. Vérifiez que les migrations SQL ont été appliquées
3. Vérifiez que votre projet Supabase est actif

## 📝 Commandes utiles

```bash
# Installation complète
npm run install:all

# Démarrage en développement
npm start

# Démarrage frontend uniquement
npm run dev:frontend

# Démarrage backend uniquement
npm run dev:backend

# Compilation pour production
npm run build

# Lancer en production (après build)
npm run start:prod
```

## ⚠️ Notes importantes

- Le serveur backend démarre même sans configuration Supabase complète
- Les routes nécessitant Supabase retourneront une erreur claire si non configuré
- Le frontend nécessite au minimum `VITE_SUPABASE_URL` et `VITE_SUPABASE_ANON_KEY` pour fonctionner

