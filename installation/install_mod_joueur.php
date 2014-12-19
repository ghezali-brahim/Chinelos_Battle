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

$reqTableNiveau = "CREATE TABLE niveau
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

$reqTableAttaque ="CREATE TABLE attaque
(
    id_attaque int PRIMARY KEY NOT NULL,
    nom VARCHAR(255) NOT NULL,
    degats int NOT NULL,
    pm_used int NOT NULL
) ENGINE=MYISAM DEFAULT CHARACTER SET=utf8;";

$connexion->query($reqTableAttaque);
?>
