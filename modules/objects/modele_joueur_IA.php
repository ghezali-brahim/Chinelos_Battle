<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "../objects/modele_participant.php";


class Joueur_IA extends Participant
{
    static    $indice_robot; // commence à 1
    protected $_id_robot;

    function __construct ( $niveauTotal )
    {
        global $indice_robot;
        if ( !isset( $indice_robot ) ) {
            $indice_robot = 0;
        }
        $indice_robot++;
        $this->_id_robot = $indice_robot;
        // Creation de l'equipe avec le niveau correspondant
        $this->_equipes = array ( Equipe::createEquipe ( $niveauTotal, $this->_id_robot ) );
    }


    function refresh ()
    {
        // TODO: Implement refresh() method.
    }

    /**
     * Appel la fonction mère pour l'affichage de la liste des personnages
     * @return mixed|string
     */
    function __toString ()
    {
        return "Identifiant robot: " . $this->_id_robot . parent::__toString ();
    }

    function getParticipant ()
    {
        return array (
                'id_robot' => $this->_id_robot,
                'equipes'  => $this->_equipes
        );
    }

    /**
     * Ajoute le personnage chez le participant
     *
     * @param $personnage
     *
     * @throws Exception
     */
    function addPersonnage ( $personnage )
    {
        if ( $personnage != NULL ) {
            $this->_equipes[ 0 ]->addPersonnage ( $personnage );
        } else {
            throw new Exception( 'Exception, ajout du personnage impossible car personnage null' );
        }
    }

    //TODO A mettre à jour
    function attaquerEnnemi ( $participant, $i )
    {
        try {
            $personnage       = $this->getEquipeOne ()->getPersonnages ()[ $i ];
            $personnageTarget = $participant->getEquipeOne ()->getPersonnagePlusFaibleVivant ();
            //ON A RAJOUTER LA NOTION D'ELEMENT
            try {
                $degats = $personnage->attaquer ( 0 );
            } catch ( Exception $e ) {
                $degats = $personnage->attaquer ( 2 );
            }
            // ON mulitplie par le ratio de l'élement
            $degats = $degats * Element::getRatioDegatElement ( $personnage->getElement (), $personnageTarget->getElement () );
            //DEBUG
            //echo '<br/>========= ' . $personnage . ' a fait ' . $degats . ' à lenemi : ' . $personnageTarget . ' ======<br/>';
            $personnageTarget->subirDegats ( $degats );
        } catch ( Exception $e ) {
            print_r ( $e );
        }
    }

    function retourneAffichageJoueurIA ()
    {
        $text = "<h2><strong>Niveau de l'equipe adverse : " . $this->getEquipeOne ()->getNiveauTotalPersos () . "</strong></h2><br>" .
                $this->getEquipeOne ()->retourneAffichageEquipe ();

        return $text;
    }
}

