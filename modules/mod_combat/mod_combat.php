<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");


define ('MOD_BPATH', "modules" . DIR_SEP . "mod_combat" . DIR_SEP);


$action = isset($_GET['action']) ? $_GET['action'] : "";
require_once(MOD_BPATH . '../include_objects.php');
require_once MOD_BPATH . "controleur" . DIR_SEP . "controleur_combat.php";

/*
if(!isset($_SESSION['controleurCombat'])){
    $controleur = new ModCombatControleurCombat();
    $_SESSION['controleurCombat']=serialize($controleur);
}else{
    $_SESSION['controleurCombat'] = implode("", @file("store"));
    $controleur=unserialize($_SESSION['controleurCombat']);

}
*/
$controleur = new ModCombatControleurCombat();

echo '<a href="index.php?module=combat">Accueil Module</a><br/>';
echo "<h2>" . $action . "</h2>";

if ($action == "afficher") {
    $controleur->afficher();
}
else if ($action == "listeCombat") {
    $controleur->listeCombat();
}
else if ($action == "Combat") {
    $controleur->combat();
}
else if ($action == "afficherTour") {
    $controleur->afficherTour();
}
else if ($action == "passerTour") {
    $controleur->passerTour();
}
else {
    $controleur->accueilModule();
}