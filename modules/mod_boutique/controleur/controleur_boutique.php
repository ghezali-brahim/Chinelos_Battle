<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");
/*
//importation modele
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_attaque.php";
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_niveau.php";
require_once MOD_BPATH . "modele" . DIR_SEP . 'modele_participant.php';
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_joueur.php";
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_joueur_IA.php";
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_personnage.php";
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_equipe.php";
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_combat.php";
*/
require_once "modules/include_objects.php";
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_boutique.php";
//importation vue
require_once MOD_BPATH . DIR_SEP . "vue/vue_boutique.php";

class ModBoutiqueControleurBoutique
{

    public function accueilModule()
    {
        ModBoutiqueVueBoutique::affAccueilModule();
    }

    public function afficher()
    {
        if (!isset($joueur)) {
            $joueur = new Boutique();
        }
        ModBoutiqueVueBoutique::afficherBoutique($joueur->getParticipant());


    }

    public function acheter()
    {
        $boutique = new Boutique();
        if (isset($_GET['value'])) {
            if ($_GET['value'] = 'personnage') {
                $boutique->acheterPerso();
                echo 'vous avez acheter un personnage à 5 gils';
                header("Refresh: 1;URL=index.php?module=boutique&action=acheter");
            }
        }
        else {
            ModBoutiqueVueBoutique::acheterPersonnage();
        }
    }

}

