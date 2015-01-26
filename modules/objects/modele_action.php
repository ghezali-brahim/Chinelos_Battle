<?php

if ( ! defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );


//TODO Action
class Action extends DBMapper {
    protected $_id_action;
    protected $_hp; //valeur du changement d'hp (négatif = perte de vie)
    protected $_mp; //valeur du changement de mp (idem)
    protected $_attaque; //même idée
    protected $_defense; //même idée

    function  __construct ( $id_action ) {
        // ICI on récupère les informations de l'action
        $requete = "SELECT DISTINCT * FROM action WHERE idAction = :idAction";
        try {
            $reponse = self::$database->prepare ( $requete );
            $reponse->execute ( array ( 'idAction' => $id_action ) );
        } catch ( PDOException $e ) {
            echo 'Échec lors de la connexion : ' . $e->getMessage ();
        }
        $actionElements = $reponse->fetch ();
        if ( $actionElements == NULL ) {
            throw new Exception( "L'identifiant element :" . $actionElements . " est une action inconnue." );
        }
        $this->_id_action = $actionElements[ 'idAction' ];
        $this->_hp        = $actionElements[ 'hp' ];
        $this->_mp        = $actionElements[ 'mp' ];
        $this->_attaque   = $actionElements[ 'attaque' ];
        $this->_defense   = $actionElements[ 'defense' ];
    }

    public function make_action ( $personnage ) {
        $personnage->addHp ( $this->_hp );
        $personnage->addMp ( $this->_mp );
        $personnage->addPuissance ( $this->_attaque );
        $personnage->addDefense ( $this->_defense );
    }
}
