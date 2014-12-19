<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");


define ('MOD_BPATH', "modules" . DIR_SEP . "mod_joueur" . DIR_SEP);


$action = isset($_GET['action']) ? $_GET['action'] : "";
require_once MOD_BPATH . "controleur" . DIR_SEP . "controleur_joueur.php";

$controleur = new ModJoueurControleurJoueur();

if ($action == "afficher") {
    $controleur->afficher();
}
else if ($action == "combat") {
    $controleur->afficherCombat();
}else if($action == "transferer"){
	$controleur->transferer();
}else {
    $controleur->accueilModule();
}
