<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");


define ('MOD_BPATH', "modules" . DIR_SEP . "mod_joueur" . DIR_SEP);

$action = isset($_GET['action']) ? $_GET['action'] : "";
require_once(MOD_BPATH . '../include_objects.php');
require_once MOD_BPATH . "controleur" . DIR_SEP . "controleur_joueur.php";

$controleur = new ModJoueurControleurJoueur();
echo '<a href="index.php?module=joueur">Accueil Module</a><br/>';
echo "<h2>" . $action . "</h2>";
if ($action == "afficher") {
    $controleur->afficher();
}
else if ($action == "transferer") {
    $controleur->transferer();
}
else if ($action == "afficherEquipeOne") {
    $controleur->afficherEquipeOne();
}
else {
    $controleur->accueilModule();
}
