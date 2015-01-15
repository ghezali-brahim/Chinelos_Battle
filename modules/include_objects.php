<?php


require_once ( "./modules/mod_connexion/modele/modele_connexion.php" );
$fichiers = glob ( "./modules/objects/*", GLOB_NOSORT );
foreach ( $fichiers as $fichier ) {
    require_once ( $fichier );
}
