<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );
define ( 'MOD_BPATH', "modules" . DIR_SEP . "mod_contact" . DIR_SEP );
$action = isset( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : "";
require_once MOD_BPATH . "controleur" . DIR_SEP . "controleur_contact.php";
$controleur = new ModContactControleurContact();
if ( $action == "envoyerMail" ) {
    $controleur->envoyerMail ();
} else {
    $controleur->accueilContact ();
}