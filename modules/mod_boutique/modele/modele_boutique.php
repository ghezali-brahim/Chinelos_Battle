<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "../objects/modele_participant.php";


class Boutique extends DBMapper
{
    protected $_joueur;

    function __construct ()
    {
        if ( Joueur::connectee () ) {
            if ( !isset( $_SESSION[ 'joueur' ] ) ) {
                $_SESSION[ 'joueur' ] = serialize ( new Joueur() );
            }
        }
        $this->_joueur = unserialize ( $_SESSION[ 'joueur' ] );
    }

    public function acheterPerso ()
    {
        try {
            $this->_joueur->depenser ( 5 );
            try {
                $this->_joueur->addPersonnage ( Personnage::createPersonnageForBD ( 1 ) );
            } catch ( Exception $e ) {
                echo $e;
                //on recredite l'argent car il y a eu une erreur
                $this->_joueur->ajouterArgent ( 5 );
            }
        } catch ( Exception $e ) {
            echo $e;
        }
        $_SESSION[ 'joueur' ] = serialize ( $this->_joueur );
    }

    // 29/11/2014
    public function acheterSoin ()
    {
        try {
            $this->_joueur->depenser ( 2 );
            try {
                $this->_joueur->soignerEquipeOne ();
            } catch ( Exception $e ) {
                echo $e;
                //on recredite l'argent car il y a eu une erreur
                $this->_joueur->ajouterArgent ( 2 );
            }
        } catch ( Exception $e ) {
            echo $e;
        }
        $_SESSION[ 'joueur' ] = serialize ( $this->_joueur );
    }
}


