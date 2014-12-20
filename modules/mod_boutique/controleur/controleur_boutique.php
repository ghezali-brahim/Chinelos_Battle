<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
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

    protected $_boutique;
    protected $_joueur;

    function __construct ()
    {
        if ( Joueur::connectee () ) {
            if ( !isset( $_SESSION[ 'joueur' ] ) ) {
                $_SESSION[ 'joueur' ] = serialize ( new Joueur() );
            }
        }
        $this->_joueur   = unserialize ( $_SESSION[ 'joueur' ] );
        $this->_boutique = new Boutique();
    }

    public function accueilModule ()
    {
        ModBoutiqueVueBoutique::affAccueilModule ();
    }

    public function afficher ()
    {
        ModBoutiqueVueBoutique::afficherJoueur ( $this->_joueur->getParticipant () );
    }

    // 29/11/2014
    public function acheter ()
    {
        if ( isset( $_GET[ 'value' ] ) ) {
            if ( $_GET[ 'value' ] == "personnage" ) {
                $this->_boutique->acheterPerso ();
                echo 'vous avez acheter un personnage à 5 gils';
                header ( "Refresh: 1;URL=index.php?module=boutique&action=acheter" );
            }
            if ( $_GET[ 'value' ] == "soin" ) {
                $this->_boutique->acheterSoin ();
                echo 'vous avez soigner tous vos personnage pour 1 gils';
                header ( "Refresh: 1;URL=index.php?module=boutique&action=acheter" );
            }
        } else {
			// Dimension faites pour 2 articles max -> CSS à refaire si changement
			echo "<div class='listeAchat'>";
            ModBoutiqueVueBoutique::acheterPersonnage ();
            ModBoutiqueVueBoutique::acheterSoin ();
			echo "</div>";
        }
    }
}

