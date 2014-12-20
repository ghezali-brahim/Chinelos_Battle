<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "../objects/modele_equipe.php";


abstract class  Participant extends DBMapper
{
    protected $_equipes;

    abstract function addPersonnage ( $personnage );

    abstract function getParticipant ();

    abstract function refresh ();

    abstract function attaquerEnnemi ( $participant, $i );

    /**
     * @param $id_personnage
     *
     * @return Personnage
     */
    function getPersonnageWithID ( $id_personnage )
    {
        $personnage = NULL;
        foreach ( $this->_equipes as $equipe ) {
            if ( $equipe->getPersonnage ( $id_personnage ) != NULL ) {
                $personnage = $equipe->getPersonnage ( $id_personnage );
            }
        }

        return $personnage;
    }

    /**
     * @return array(Equipes)
     */
    function getEquipes ()
    {
        return $this->_equipes;
    }

    /**
     * Retourne le niveau total de l'equipe one du participant
     * @return int
     */
    function getNiveauTotalParticipant ()
    {
        return $this->getEquipeOne ()->getNiveauTotalPersos ();
    }

    /**
     * @return Equipe 0
     */
    function getEquipeOne ()
    {
        return $this->_equipes[ 0 ];
    }

    /**
     * Ici on retire les morts de l'equipe, ensuite on trie l'equipe principal
     */
    function virerMortEquipeOne ()
    {
        $listePersonnageATransferer = array ();
        foreach ( $this->_equipes[ 0 ]->getPersonnages () as $personnage ) {
            if ( $personnage->isDead () ) {
                array_push ( $listePersonnageATransferer, $personnage );
            }
        }
        foreach ( $listePersonnageATransferer as $personnage ) {
            $this->transferer ( $personnage );
        }
    }

    /**
     * Modifie l'equipe du personnage spécifié du participant
     *
     * @param $personnage
     *
     * @throws Exception
     */
    function transferer ( $personnage )
    {
        if ( $personnage == NULL ) {
            throw new Exception( 'personnage inconnu' );
        }
        if ( $personnage->getIdEquipe () == $this->_equipes[ 0 ]->getIdEquipe () ) {
            $this->_equipes[ 0 ]->removePersonnage ( $personnage );
            $this->_equipes[ 1 ]->addPersonnage ( $personnage );
        } else if ( $personnage->getIdEquipe () == $this->_equipes[ 1 ]->getIdEquipe () ) {
            if ( $this->getEquipeOne ()->getNombrePersonnages () < 6 ) {
                $this->_equipes[ 1 ]->removePersonnage ( $personnage );
                $this->_equipes[ 0 ]->addPersonnage ( $personnage );
            } else {
                throw new Exception( "Impossible de transferer le personnage car l'equipe principal est déja remplit (6personnage)." );
            }
        } else {
            throw new Exception( 'transfert impossible' );
        }
    }

    function trierEquipeOne ()
    {
        $this->_equipes[ 0 ]->trierPersonnageParNiveau ();
    }

    /**
     * @return string
     */
    function __toString ()
    {
        return "; " . $this->_equipes[ 0 ]->__toString ();
    }

    /**
     * Soigne l'equipe One du participant
     */
    function soignerEquipeOne ()
    {
        $this->getEquipeOne ()->soignerEquipe ();
    }
}

