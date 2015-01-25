<?php
if ( ! defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );



/**
 * Created by PhpStorm.
 * User: Caporal
 * Date: 25/01/2015
 * Time: 17:56
 */
class Message extends DBMapper {
    protected $_id_message;
    protected $_objet;
    protected $_contenu;
    protected $_id_expeditaire;
    protected $_id_destinataire;
    protected $_date_envoie;
    protected $_lu;
    protected $_username_expeditaire;
    protected $_username_destinataire;

    function __construct ( $id_message ) {
        $resultat               = self::requeteFromDB ( "select objet, contenu, id_expeditaire, id_destinataire, date_envoie, lu from messages where id_message=:id_message", array ( 'id_message' => $id_message ) )[ 0 ];
        $this->_objet           = $resultat[ 'objet' ];
        $this->_contenu         = $resultat[ 'contenu' ];
        $this->_id_destinataire = $resultat[ 'id_expeditaire' ];
        $this->_id_expeditaire  = $resultat[ 'id_expeditaire' ];
        $this->_date_envoie     = $resultat[ 'date_envoie' ];
        $this->_lu              = $resultat[ 'lu' ];
        $this->_username_expeditaire=Joueur::getUsernameJoueur($this->_id_expeditaire);
        $this->_username_destinataire=Joueur::getUsernameJoueur($this->_id_destinataire);
    }


    static function createMessage ( $objet, $contenu, $id_destinataire, $id_expeditaire ) {
        $donneesMessage = array ( 'objet' => $objet, 'contenu' => $contenu, 'id_destinataire' => $id_destinataire, 'id_expeditaire' => $id_expeditaire );
        self::requeteFromDB ( "INSERT INTO messages (objet, contenu, id_expeditaire, id_destinataire,date_envoie) VALUES(:objet, :contenu, :id_destinataire, :id_expeditaire, NOW())", $donneesMessage );
    }


    function getMessageArray () {
        $contenuMessage = array ( 'id_message' => $this->_id_message, 'objet' => $this->_objet, 'contenu' => $this->_contenu, 'id_expeditaire' => $this->_id_expeditaire, 'id_destinaire' => $this->_id_destinataire, 'username_expeditaire' => $this->_username_expeditaire, 'username_destinataire' => $this->_username_destinataire, 'date_envoie' => $this->_date_envoie, 'lu' => $this->_lu );

        return $contenuMessage;
    }
}
