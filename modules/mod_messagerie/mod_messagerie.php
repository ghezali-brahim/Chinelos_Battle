<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );
define ( 'MOD_BPATH', "modules" . DIR_SEP . "mod_messagerie" . DIR_SEP );
$action = isset( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : "";
require_once ( MOD_BPATH . '../include_objects.php' );
require_once MOD_BPATH . "controleur" . DIR_SEP . "controleur_messagerie.php";
if ( !Joueur::connectee () ) {
    header ( 'Location: index.php?module=connexion' );
    exit();
}
$controleur = new ModMessagerieControleurMessagerie();
echo '<a href="index.php?module=messagerie">Accueil Module</a><br/>';
//echo "<h2>" . $action . "</h2>";
if ( $action == "afficher" ) {
    $controleur->afficherListeMessages ();
} else if ( $action == "envoyer_message" ) {
    $controleur->envoyerMessage ();
} else {
    $controleur->accueilModule ();
}
