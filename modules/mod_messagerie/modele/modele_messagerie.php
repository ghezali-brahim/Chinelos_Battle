<?php
if ( ! defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class ModMessagerieModeleMessagerie extends DBMapper {
    protected $_id_message;
    protected $_objet;
    protected $_contenu;
    protected $_id_expeditaire;
    protected $_id_destinataire;
    protected $_date_envoie;
    protected $_lu;

    function __construct ( $id_message ) {
        $resultat               = self::requeteFromDB ( "select objet, contenu, id_expeditaire, id_destinataire, date_envoie, lu from messages where id_message=:id_message", array ( 'id_message' => $id_message ) )[ 0 ];
        $this->_objet           = $resultat[ 'objet' ];
        $this->_contenu         = $resultat[ 'contenu' ];
        $this->_id_destinataire = $resultat[ 'id_expeditaire' ];
        $this->_id_expeditaire  = $resultat[ 'id_expeditaire' ];
        $this->_date_envoie     = $resultat[ 'date_envoie' ];
        $this->_lu              = $resultat[ 'lu' ];
    }

    static function getMessages () {
        $user               = unserialize ( $_SESSION[ 'user' ] );
        $listes_id_messages = self::requeteFromDB ( "select id_message from messages where id_expeditaire=:id_user", array ( 'id_user' => $user->getIdUser () ) );
        $messages           = array ();
        foreach ( $listes_id_messages as $id_message ) {
            $messages = new ModMessagerieModeleMessagerie( $id_message[ 'id_message' ] );
        }

        return $messages;
    }

    static function createMessage () {
        $user = unserialize ( $_SESSION[ 'user' ] );
        //TODO
    }

    function getMessagerieArray () {
        $contenuMessage = array ( 'id_message' => $this->_id_message, 'objet' => $this->_objet, 'contenu' => $this->_contenu, 'id_expeditaire' => $this->_id_expeditaire, 'id_destinaire' => $this->_id_destinataire, 'date_envoie' => $this->_date_envoie, 'lu' => $this->_lu );

        return $contenuMessage;
    }
}

