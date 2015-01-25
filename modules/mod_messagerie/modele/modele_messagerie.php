<?php
if ( ! defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class ModMessagerieModeleMessagerie extends DBMapper {

    protected $_id_user;
    private $_liste_messages_envoyer;
    private $_liste_messages_recus;

    function __construct ( $id_user ) {
        $this->_id_user                = $id_user;
        $liste_id_messages_envoyer     = self::requeteFromDB ( "select id_message from messages where id_expeditaire=:id_user", array ( 'id_user' => $this->_id_user ) );
        $liste_id_messages_recus       = self::requeteFromDB ( "select id_message from messages where id_destinataire=:id_user", array ( 'id_user' => $this->_id_user ) );
        $this->_liste_messages_envoyer = array ();
        $this->_liste_message_recus    = array ();
        foreach ( $liste_id_messages_envoyer as $id_message ) {
            array_push ( $this->_liste_messages_envoyer, new Message( $id_message ) );
        }
        foreach ( $liste_id_messages_recus as $id_message ) {
            array_push ( $this->_liste_messages_recus, new Message( $id_message ) );
        }
    }


    function createMessage ( $objet, $contenu, $id_destinataire ) {
        Message::createMessage ( $objet, $contenu, $id_destinataire, $this->_id_user );
    }
    function getListesMessagesEnvoyer(){
        $listesMessagesEnvoyerArray=array();
        if(count($this->_liste_messages_envoyer)<=0){
            return array();
        }
        foreach( $this->_liste_messages_envoyer as $message_envoyer){
            array_push($listesMessagesEnvoyerArray,$message_envoyer->getMessagerieArray());
        }
        return $listesMessagesEnvoyerArray;
    }
    function getListesMessagesRecus(){
        $listesMessagesRecusArray=array();
        if(count($this->_liste_messages_recus)<=0){
            return array();
        }
        foreach( $this->_liste_messages_recus as $message_recus){
            array_push($listesMessagesRecusArray,$message_recus->getMessageArray());
        }
        return $listesMessagesRecusArray;
    }

}

