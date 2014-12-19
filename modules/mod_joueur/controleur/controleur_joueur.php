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
//importation vue
require_once MOD_BPATH . DIR_SEP . "vue/vue_profil.php";

class ModJoueurControleurJoueur
{

    public function accueilModule()
    {
        ModJoueurVueJoueur::affAccueilModule();
    }

    public function afficher()
    {
        if (!isset($joueur)) {
            $joueur = new Joueur();
        }
        ModJoueurVueJoueur::afficherJoueur($joueur->getParticipant());
    }

    public function afficherEquipeOne()
    {
        if (!isset($joueur)) {
            $joueur = new Joueur();
        }
        ModJoueurVueJoueur::afficherEquipeOne($joueur->getEquipeOne());
    }

    public function transferer()
    {
        $joueur = new Joueur();
        if (isset($_GET['id_personnage'])) {
            $id_personnage = $_GET['id_personnage'];
            $personnage    = $joueur->getPersonnageWithID($id_personnage);
            $joueur->transferer($personnage);
            header("Refresh: 0;URL=index.php?module=joueur&action=transferer");
            //echo '<a href="index.php?module=joueur&action=afficher">Rafraichir</a>';
        }
        else {
            ModJoueurVueJoueur::afficherTransfert($joueur->getParticipant());
        }

    }


}

