-- Migration: Ajouter la colonne image_url à la table personnage
-- Date: 2024-01-XX

-- Ajouter la colonne image_url si elle n'existe pas déjà
ALTER TABLE personnage 
ADD COLUMN IF NOT EXISTS image_url TEXT DEFAULT NULL;

-- Ajouter un commentaire pour documenter la colonne
COMMENT ON COLUMN personnage.image_url IS 'URL de l\'image générée par IA pour le personnage (Replicate/OpenAI DALL-E)';

