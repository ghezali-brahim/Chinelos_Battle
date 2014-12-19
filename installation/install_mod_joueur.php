<?php

$reqTablePersonnage = "CREATE TABLE IF NOT EXISTS personnage (
id_personnage SERIAL,
nom VARCHAR( 255 ) NOT NULL,
element INT,
niveau INT,
experience INT,
attaques VARCHAR( 255 ) ,
hp INT ,
hp_max INT ,
mp INT,
mp_max INT ,
puissance INT,
defense INT,
id_user INT
) ENGINE=MYISAM DEFAULT CHARACTER SET=utf8;";

$connexion->query($reqTablePersonnage);

$reqTableNiveau = "CREATE TABLE IF NOT EXISTS niveau
(
    niveau int PRIMARY KEY NOT NULL,
    experience bigint NOT NULL,
    hp_max int NOT NULL,
    mp_max int NOT NULL,
    puissance int NOT NULL,
    defense int NOT NULL
);
ALTER TABLE niveau ADD CONSTRAINT unique_experience UNIQUE (experience);
";
$connexion->query($reqTableNiveau);

$reqTableAttaque = "CREATE TABLE IF NOT EXISTS attaque
(
    id_attaque int PRIMARY KEY NOT NULL,
    nom VARCHAR(255) NOT NULL,
    degats int NOT NULL,
    pm_used int NOT NULL
) ENGINE=MYISAM DEFAULT CHARACTER SET=utf8;";

$connexion->query($reqTableAttaque);

$reqTableAttaqueInsert = " INSERT INTO test.attaque (id_attaque, nom, degats, pm_used) VALUES (1, 'attaque1', 10, 1);
INSERT INTO test.attaque (id_attaque, nom, degats, pm_used) VALUES (2, 'attaque2', 50, 6);
INSERT INTO test.attaque (id_attaque, nom, degats, pm_used) VALUES (3, 'attaque3', 5, 0);
";
$connexion->query($reqTableAttaqueInsert);

$reqTableNiveauInsert = "INSERT INTO test.niveau (niveau, experience, hp_max, mp_max, puissance, defense) VALUES (1, 0, 5, 3, 2, 0);
INSERT INTO test.niveau (niveau, experience, hp_max, mp_max, puissance, defense) VALUES (2, 10, 10, 5, 3, 1);
INSERT INTO test.niveau (niveau, experience, hp_max, mp_max, puissance, defense) VALUES (3, 30, 15, 7, 4, 2);
";
$connexion->query($reqTableNiveauInsert);

