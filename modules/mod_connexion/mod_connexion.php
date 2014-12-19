<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");


define ('MOD_BPATH', "modules" . DIR_SEP . "mod_connexion" . DIR_SEP);


$action = isset($_GET['action']) ? $_GET['action'] : "";

if ($action == "connexion") {

    include MOD_BPATH . "controleur" . DIR_SEP . "controleur_connexion.php";
    $controleur = new ModConnexionControleurConnexion();
    $controleur->connexion();
}
elseif ($action == "deconnexion") {
    include MOD_BPATH . "controleur" . DIR_SEP . "controleur_connexion.php";
    $controleur = new ModConnexionControleurConnexion();
    $controleur->deconnexion();
}
else {
    include MOD_BPATH . "controleur" . DIR_SEP . "controleur_connexion.php";
    $controleur = new ModConnexionControleurConnexion();
    $controleur->accueilModule();
}

