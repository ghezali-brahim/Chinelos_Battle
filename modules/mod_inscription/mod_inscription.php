<?php
if ( ! defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );
define ( 'MOD_BPATH', "modules" . DIR_SEP . "mod_inscription" . DIR_SEP );
if ( isset( $_SESSION[ 'id_user' ] ) && $_SESSION[ 'id_user' ] != NULL )
    exit ( "Vous etes deja connectee" );
$action = isset( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : "";
if ( $action == "inscription" ) {
    include MOD_BPATH . "controleur" . DIR_SEP . "controleur_inscription.php";
    $controleur = new ModInscriptionControleurInscription();
    $controleur->inscription ();
} else {
    include MOD_BPATH . "controleur" . DIR_SEP . "controleur_inscription.php";
    $controleur = new ModInscriptionControleurInscription();
    $controleur->accueilModule ();
}

