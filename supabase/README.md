# Configuration Supabase

Ce dossier contient les migrations et la configuration pour Supabase.

## Installation

1. Installer Supabase CLI:
```bash
npm install -g supabase
```

2. Initialiser Supabase localement (optionnel pour développement):
```bash
supabase init
```

3. Démarrer Supabase localement:
```bash
supabase start
```

4. Appliquer les migrations:
```bash
supabase db reset
```

## Production

Pour déployer sur Supabase Cloud:

1. Créer un projet sur https://supabase.com
2. Lier le projet local au projet cloud:
```bash
supabase link --project-ref your-project-ref
```

3. Déployer les migrations:
```bash
supabase db push
```

## Variables d'environnement

Créer un fichier `.env` dans `frontend/` et `backend/` avec:

```
VITE_SUPABASE_URL=https://your-project.supabase.co
VITE_SUPABASE_ANON_KEY=your-anon-key
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_SERVICE_ROLE_KEY=your-service-role-key
```

## Structure

- `migrations/` - Fichiers de migration SQL
- `config.toml` - Configuration Supabase locale

