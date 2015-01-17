<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );
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
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_messagerie.php";
require_once MOD_BPATH . DIR_SEP . "vue/vue_messagerie.php";


class ModMessagerieControleurMessagerie
{
    protected $_joueur;

    function __construct ()
    {
        if ( Joueur::connectee () ) {
            if ( !isset( $_SESSION[ 'joueur' ] ) ) {
                $_SESSION[ 'joueur' ] = serialize ( new Joueur() );
            } else {
                $_SESSION[ 'joueur' ] = serialize ( new Joueur() );
            }
        }
        $this->_joueur = unserialize ( $_SESSION[ 'joueur' ] );
    }

    public function accueilModule ()
    {
        ModMessagerieVueMessagerie::affAccueilModule ();
    }

    public function afficherMessage ()
    {
        //ModMessagerieVueMessagerie::afficherMessage($message)
    }

    public function afficherListeMessages ()
    {
        $messages = ModMessagerieModeleMessagerie::getMessages ();
        ModMessagerieVueMessagerie::afficherlistesMessages ( $messages );
    }

    public function envoyerMessage ()
    {
        ModMessagerieVueMessagerie::afficherFormEnvoieMessage ();
    }
}
