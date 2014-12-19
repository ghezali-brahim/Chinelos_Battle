<?php

$reqTableVin = "CREATE TABLE IF NOT EXISTS mod_vin_vin (
id SERIAL,
nom_domaine TEXT,
appellation BIGINT UNSIGNED NOT NULL,
denomination VARCHAR(200),
cepage_dominant BIGINT UNSIGNED NOT NULL,
couleur VARCHAR(5),
annee INT(4),
dateajout DATETIME,
FULLTEXT (nom_domaine)
) ENGINE=MYISAM DEFAULT CHARACTER SET=utf8;";

$reqTableAppellation = "CREATE TABLE IF NOT EXISTS mod_vin_appellation (
id SERIAL,
nom VARCHAR(200),
idregion BIGINT UNSIGNED NOT NULL
) ENGINE=MYISAM DEFAUlT CHARACTER SET=utf8;";

$reqTableRegion = "CREATE TABLE IF NOT EXISTS mod_vin_region (
id SERIAL,
nom VARCHAR(100)
) ENGINE=MYISAM DEFAUlT CHARACTER SET=utf8;";

$reqTableCepage = "CREATE TABLE IF NOT EXISTS mod_vin_cepage (
id SERIAL,
nom VARCHAR(100),
couleur VARCHAR(5)
) ENGINE=MYISAM DEFAUlT CHARACTER SET=utf8;";


$reqTableAdmin = "CREATE TABLE IF NOT EXISTS mod_vin_admin (
id_user BIGINT UNSIGNED NOT NULL,
droit_ajout int(4),
droit_modif int (4),
droit_supp int(4),
PRIMARY KEY (id_user)
) ENGINE=MYISAM DEFAUlT CHARACTER SET=utf8;";

$connexion->query($reqTableVin);
$connexion->query($reqTableAppellation);
$connexion->query($reqTableRegion);
$connexion->query($reqTableCepage);
$connexion->query($reqTableAdmin);

$reqInsert = "INSERT INTO mod_vin_vin (nom_domaine, appellation, denomination, cepage_dominant, couleur, annee, dateajout)
VALUES('La Chablisienne', 1, '',1, 'blanc', 2010, NOW())";
$connexion->query($reqInsert);
$reqInsert = "INSERT INTO mod_vin_vin (nom_domaine, appellation, denomination, cepage_dominant, couleur, annee, dateajout)
VALUES('La Chablisienne', 2, 'Les Preuses', 1, 'blanc', 2010, NOW())";
$connexion->query($reqInsert);
$reqInsert = "INSERT INTO mod_vin_appellation (nom, idregion) VALUES('Petit Chablis', 1)";
$connexion->query($reqInsert);
$reqInsert = "INSERT INTO mod_vin_appellation (nom, idregion) VALUES('Chablis Grand Cru', 1)";
$connexion->query($reqInsert);
$reqInsert = "INSERT INTO mod_vin_region (nom) VALUES('Bourgogne')";
$connexion->query($reqInsert);
$reqInsert = "INSERT INTO mod_vin_region (nom) VALUES('Bordeaux')";
$connexion->query($reqInsert);
$reqInsert = "INSERT INTO mod_vin_cepage (nom, couleur) VALUES('Chardonnay B', 'blanc')";
$connexion->query($reqInsert);



?>
