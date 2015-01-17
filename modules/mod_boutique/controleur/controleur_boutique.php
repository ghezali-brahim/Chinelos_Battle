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
                if ( !isset( $_GET[ 'id_element' ] ) || !isset( $_GET[ 'nom_personnage' ] ) ) {
                    ModBoutiqueVueBoutique::formAchatPersonnage ();
                } else {
                    $id_element     = $_GET[ 'id_element' ];
                    $nom_personnage = $_GET[ 'nom_personnage' ];
                    if ( preg_match ( '/^[a-zA-Z0-9_]{4,}$/', $nom_personnage ) ) {
                        $this->_boutique->acheterPerso ( $nom_personnage, $id_element );
                        echo 'vous avez acheter un personnage à 5 gils';
                        header ( "Refresh: 1;URL=index.php?module=boutique&action=acheter" );
                        exit ();
                    } else {
                        echo "Nom incorrecte, taille mini 4: " . $_GET[ 'nom_personnage' ] . " ; Pas de caractère spéciaux ni d'espace SVP";
                        echo "<br> Les seuls caractères autorisés sont : alphanumerique ET '_'";
                        unset( $_GET[ 'nom_personnage' ] );
                        ModBoutiqueVueBoutique::formAchatPersonnage ();
                    }
                }
            } else if ( $_GET[ 'value' ] == "soin" ) {
                $this->_boutique->acheterSoin ();
                echo 'vous avez soigner tous vos personnage pour 1 gils';
                header ( "Refresh: 1;URL=index.php?module=boutique&action=acheter" );
                exit();
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

