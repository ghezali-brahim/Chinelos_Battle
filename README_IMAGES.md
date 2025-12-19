# Génération d'Images de Personnages

Ce projet intègre la génération automatique d'images de personnages business/entrepreneurs en utilisant des APIs d'IA.

## Configuration

### Option 1: Replicate (Recommandé - Stable Diffusion)

1. Créer un compte sur [Replicate](https://replicate.com)
2. Obtenir votre API token depuis [votre profil](https://replicate.com/account/api-tokens)
3. Ajouter dans `backend/.env`:
   ```
   REPLICATE_API_TOKEN=votre_token_replicate
   ```

**Avantages:**
- Stable Diffusion XL pour des images de qualité
- Prix raisonnable
- Rapide

### Option 2: OpenAI DALL-E 3 (Meilleure qualité)

1. Créer un compte sur [OpenAI](https://platform.openai.com)
2. Obtenir votre API key depuis [API Keys](https://platform.openai.com/api-keys)
3. Ajouter dans `backend/.env`:
   ```
   OPENAI_API_KEY=votre_clé_openai
   ```

**Avantages:**
- Images de très haute qualité
- Style très réaliste
- Plus cher mais meilleure qualité

## Fonctionnement

1. Lorsqu'un personnage est créé ou affiché pour la première fois, l'application génère automatiquement une image
2. L'image est sauvegardée dans la base de données (colonne `image_url`)
3. Les images suivantes utilisent l'image en cache
4. Si une API n'est pas configurée, l'application utilise un fallback avec des emojis/avatars CSS

## Endpoints API

### POST /api/images/generate
Génère une image pour un personnage.

**Body:**
```json
{
  "id_personnage": 1,
  "element": 2,
  "niveau": 5,
  "nom": "CEO_Tech",
  "isEnemy": false
}
```

### GET /api/images/character/:id
Récupère ou génère l'image d'un personnage.

## Styles de Personnages

Les images générées varient selon:
- **Élément**: Couleur et style de costume (Normal=bleu marine, Feu=rouge, Eau=bleu, Plante=vert)
- **Niveau**: Style vestimentaire (Junior=casual, Senior=formel, Executive=premium)
- **Nom**: Personnalité et spécialité business

## Migration Base de Données

La colonne `image_url` a été ajoutée à la table `personnage`. Exécutez la migration Supabase ou ajoutez manuellement:

```sql
ALTER TABLE personnage ADD COLUMN image_url TEXT DEFAULT NULL;
```

