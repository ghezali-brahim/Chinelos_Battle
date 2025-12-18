-- Migration du schéma MySQL vers PostgreSQL/Supabase
-- Chinelos Battle - Schema initial

-- Extension pour UUID
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Table des éléments (feu, eau, plante, normal)
CREATE TABLE IF NOT EXISTS element (
  id_element SERIAL PRIMARY KEY,
  nom VARCHAR(64) NOT NULL UNIQUE,
  id_faible_contre VARCHAR(64) DEFAULT NULL,
  id_fort_contre VARCHAR(64) DEFAULT NULL
);

-- Données initiales des éléments
INSERT INTO element (id_element, nom, id_faible_contre, id_fort_contre) VALUES
(1, 'Normal', '', NULL),
(2, 'Feu', '3', '4'),
(3, 'Eau', '4', '2'),
(4, 'Plante', '2', '3')
ON CONFLICT (id_element) DO NOTHING;

-- Table des attaques
CREATE TABLE IF NOT EXISTS attaque (
  id_attaque SERIAL PRIMARY KEY,
  nom VARCHAR(255) NOT NULL,
  degats INTEGER NOT NULL,
  mp_used INTEGER DEFAULT NULL
);

-- Données initiales des attaques
INSERT INTO attaque (id_attaque, nom, degats, mp_used) VALUES
(1, 'attaque1', 10, 1),
(2, 'attaque2', 50, 6),
(3, 'attaque3', 5, 0)
ON CONFLICT (id_attaque) DO NOTHING;

-- Table des niveaux et expérience
CREATE TABLE IF NOT EXISTS niveau (
  niveau INTEGER PRIMARY KEY,
  experience BIGINT NOT NULL UNIQUE
);

-- Données initiales des niveaux
INSERT INTO niveau (niveau, experience) VALUES
(1, 0),
(2, 10),
(3, 30),
(4, 60),
(5, 90),
(6, 120),
(7, 180),
(8, 240),
(9, 300),
(10, 500),
(11, 600),
(12, 700)
ON CONFLICT (niveau) DO NOTHING;

-- Table des actions (pour les items)
CREATE TABLE IF NOT EXISTS action (
  id_action SERIAL PRIMARY KEY,
  hp INTEGER NOT NULL,
  mp INTEGER NOT NULL,
  attaque INTEGER NOT NULL,
  defense INTEGER NOT NULL
);

-- Table des types d'items
CREATE TABLE IF NOT EXISTS item_type (
  id_type SERIAL PRIMARY KEY,
  nom VARCHAR(64) NOT NULL
);

-- Table des items
CREATE TABLE IF NOT EXISTS item (
  id_item SERIAL PRIMARY KEY,
  nom VARCHAR(64) NOT NULL,
  description TEXT NOT NULL,
  prix_achat INTEGER NOT NULL,
  type INTEGER NOT NULL REFERENCES item_type(id_type),
  id_action INTEGER NOT NULL REFERENCES action(id_action)
);

-- Table des utilisateurs (étendue avec les données du jeu)
-- Note: Supabase Auth gère déjà la table auth.users
-- On crée une table profile qui référence auth.users
CREATE TABLE IF NOT EXISTS users (
  id_user UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  username VARCHAR(255) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL,
  argent INTEGER DEFAULT 0,
  nombre_victoire INTEGER DEFAULT 0,
  nombre_defaite INTEGER DEFAULT 0,
  last_connection TIMESTAMPTZ DEFAULT NOW(),
  connected BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table des équipes
CREATE TABLE IF NOT EXISTS equipe (
  id_equipe SERIAL PRIMARY KEY,
  id_user UUID NOT NULL REFERENCES users(id_user) ON DELETE CASCADE
);

-- Table des personnages
CREATE TABLE IF NOT EXISTS personnage (
  id_personnage SERIAL PRIMARY KEY,
  nom VARCHAR(255) NOT NULL UNIQUE,
  element INTEGER REFERENCES element(id_element),
  niveau INTEGER DEFAULT 1,
  experience INTEGER DEFAULT 0,
  attaques VARCHAR(255) DEFAULT NULL, -- Format: "1;2;3"
  hp INTEGER DEFAULT 10,
  hp_max INTEGER DEFAULT 10,
  mp INTEGER DEFAULT 5,
  mp_max INTEGER DEFAULT 5,
  puissance INTEGER DEFAULT 3,
  defense INTEGER DEFAULT 1,
  id_equipe INTEGER REFERENCES equipe(id_equipe) ON DELETE SET NULL
);

-- Table des combats
CREATE TABLE IF NOT EXISTS combats (
  id_combat SERIAL PRIMARY KEY,
  id_joueur_1 UUID NOT NULL REFERENCES users(id_user),
  id_joueur_2 UUID REFERENCES users(id_user), -- NULL pour combat contre IA
  indice_perso_j1 INTEGER NOT NULL DEFAULT -1,
  indice_perso_j2 INTEGER NOT NULL DEFAULT -1,
  valider_j1 BOOLEAN NOT NULL DEFAULT FALSE,
  valider_j2 BOOLEAN NOT NULL DEFAULT FALSE,
  finit BOOLEAN NOT NULL DEFAULT FALSE,
  nombre_tour INTEGER NOT NULL DEFAULT 0,
  indice_tour_de_joueur INTEGER NOT NULL DEFAULT 0,
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Table de l'inventaire
CREATE TABLE IF NOT EXISTS inventaire (
  id_user UUID NOT NULL REFERENCES users(id_user) ON DELETE CASCADE,
  id_item INTEGER NOT NULL REFERENCES item(id_item),
  quantite INTEGER NOT NULL DEFAULT 1,
  PRIMARY KEY (id_user, id_item)
);

-- Table des messages
CREATE TABLE IF NOT EXISTS messages (
  id_message SERIAL PRIMARY KEY,
  objet VARCHAR(255) NOT NULL,
  contenu TEXT NOT NULL,
  id_expeditaire UUID NOT NULL REFERENCES users(id_user),
  id_destinataire UUID NOT NULL REFERENCES users(id_user),
  date_envoie TIMESTAMPTZ DEFAULT NOW(),
  lu BOOLEAN NOT NULL DEFAULT FALSE
);

-- Index pour améliorer les performances
CREATE INDEX IF NOT EXISTS idx_personnage_equipe ON personnage(id_equipe);
CREATE INDEX IF NOT EXISTS idx_equipe_user ON equipe(id_user);
CREATE INDEX IF NOT EXISTS idx_combats_joueur1 ON combats(id_joueur_1);
CREATE INDEX IF NOT EXISTS idx_combats_joueur2 ON combats(id_joueur_2);
CREATE INDEX IF NOT EXISTS idx_messages_expeditaire ON messages(id_expeditaire);
CREATE INDEX IF NOT EXISTS idx_messages_destinataire ON messages(id_destinataire);
CREATE INDEX IF NOT EXISTS idx_messages_date ON messages(date_envoie);

-- Fonction pour mettre à jour updated_at automatiquement
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ language 'plpgsql';

-- Triggers pour updated_at
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
  FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_combats_updated_at BEFORE UPDATE ON combats
  FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Row Level Security (RLS) Policies

-- Activer RLS sur toutes les tables
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE equipe ENABLE ROW LEVEL SECURITY;
ALTER TABLE personnage ENABLE ROW LEVEL SECURITY;
ALTER TABLE combats ENABLE ROW LEVEL SECURITY;
ALTER TABLE inventaire ENABLE ROW LEVEL SECURITY;
ALTER TABLE messages ENABLE ROW LEVEL SECURITY;

-- Policy: Les utilisateurs peuvent lire leur propre profil
CREATE POLICY "Users can read own profile" ON users
  FOR SELECT USING (auth.uid() = id_user);

-- Policy: Les utilisateurs peuvent mettre à jour leur propre profil
CREATE POLICY "Users can update own profile" ON users
  FOR UPDATE USING (auth.uid() = id_user);

-- Policy: Les utilisateurs peuvent lire les équipes qu'ils possèdent
CREATE POLICY "Users can read own teams" ON equipe
  FOR SELECT USING (auth.uid() = id_user);

-- Policy: Les utilisateurs peuvent créer leurs équipes
CREATE POLICY "Users can create own teams" ON equipe
  FOR INSERT WITH CHECK (auth.uid() = id_user);

-- Policy: Les utilisateurs peuvent lire leurs personnages
CREATE POLICY "Users can read own characters" ON personnage
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM equipe WHERE equipe.id_equipe = personnage.id_equipe
      AND equipe.id_user = auth.uid()
    )
  );

-- Policy: Les utilisateurs peuvent créer leurs personnages
CREATE POLICY "Users can create own characters" ON personnage
  FOR INSERT WITH CHECK (
    id_equipe IS NULL OR EXISTS (
      SELECT 1 FROM equipe WHERE equipe.id_equipe = personnage.id_equipe
      AND equipe.id_user = auth.uid()
    )
  );

-- Policy: Les utilisateurs peuvent lire leurs combats
CREATE POLICY "Users can read own combats" ON combats
  FOR SELECT USING (auth.uid() = id_joueur_1 OR auth.uid() = id_joueur_2);

-- Policy: Les utilisateurs peuvent créer leurs combats
CREATE POLICY "Users can create own combats" ON combats
  FOR INSERT WITH CHECK (auth.uid() = id_joueur_1);

-- Policy: Les utilisateurs peuvent lire leur inventaire
CREATE POLICY "Users can read own inventory" ON inventaire
  FOR SELECT USING (auth.uid() = id_user);

-- Policy: Les utilisateurs peuvent lire les messages qu'ils ont envoyés ou reçus
CREATE POLICY "Users can read own messages" ON messages
  FOR SELECT USING (auth.uid() = id_expeditaire OR auth.uid() = id_destinataire);

-- Policy: Les utilisateurs peuvent créer des messages
CREATE POLICY "Users can create messages" ON messages
  FOR INSERT WITH CHECK (auth.uid() = id_expeditaire);

-- Policy: Les utilisateurs peuvent mettre à jour les messages qu'ils ont reçus
CREATE POLICY "Users can update received messages" ON messages
  FOR UPDATE USING (auth.uid() = id_destinataire);

-- Les tables de référence (element, attaque, niveau, item, etc.) sont publiques en lecture
CREATE POLICY "Public read access to elements" ON element FOR SELECT TO authenticated USING (true);
CREATE POLICY "Public read access to attaques" ON attaque FOR SELECT TO authenticated USING (true);
CREATE POLICY "Public read access to niveaux" ON niveau FOR SELECT TO authenticated USING (true);
CREATE POLICY "Public read access to items" ON item FOR SELECT TO authenticated USING (true);
CREATE POLICY "Public read access to item_types" ON item_type FOR SELECT TO authenticated USING (true);
CREATE POLICY "Public read access to actions" ON action FOR SELECT TO authenticated USING (true);

-- Fonction pour créer automatiquement un profil utilisateur lors de l'inscription
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
  INSERT INTO public.users (id_user, username, email)
  VALUES (
    NEW.id,
    COALESCE(NEW.raw_user_meta_data->>'username', NEW.email),
    NEW.email
  );
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Trigger pour créer automatiquement le profil
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE FUNCTION public.handle_new_user();

