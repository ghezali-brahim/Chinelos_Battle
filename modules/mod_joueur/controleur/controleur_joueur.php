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
require_once MOD_BPATH . DIR_SEP . "vue/vue_profil.php";


class ModJoueurControleurJoueur
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
        ModJoueurVueJoueur::affAccueilModule ();
    }

    public function afficher ()
    {
        ModJoueurVueJoueur::afficherJoueur ( $this->_joueur->getParticipant () );
    }

    public function afficherEquipeOne ()
    {
        ModJoueurVueJoueur::afficherEquipeOne ( $this->_joueur->getEquipeOne () );
    }

    public function transferer ()
    {
        if ( isset( $_GET[ 'id_personnage' ] ) ) {
            $id_personnage = $_GET[ 'id_personnage' ];
            $personnage    = $this->_joueur->getPersonnageWithID ( $id_personnage );
            $this->_joueur->transferer ( $personnage );
            $_SESSION[ 'joueur' ] = serialize ( $this->_joueur );
            header ( "Refresh: 0;URL=index.php?module=joueur&action=transferer" );
            exit();
            //echo '<a href="index.php?module=joueur&action=afficher">Rafraichir</a>';
        } else {
            ModJoueurVueJoueur::afficherTransfert ( $this->_joueur->getParticipant () );
        }
    }

    //TODO classement
    public function classement ()
    {
        if ( isset( $_GET[ 'type' ] ) ) {
            $type = $_GET[ 'type' ];
            switch ( $type ) {
                case 'chinelos':
                    $donnees = Joueur::getAllPersoClassement ();
                    ModJoueurVueJoueur::afficherClassementPersonnagesEtJoueur ( $donnees );
                    break;
                case 'joueurs':
                    $donnees = Joueur::getAllJoueurClassement ();
                    ModJoueurVueJoueur::afficherClassementJoueur ( $donnees );
                    break;
                default:
                    ModJoueurVueJoueur::afficherChoixClassement ();
                    break;
            }
        } else {
            ModJoueurVueJoueur::afficherChoixClassement ();
        }
    }
}
