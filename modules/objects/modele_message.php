<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
/*
 * table message : id_message, contenu, id_scenario
 *
 */


//TODO non fonctionnel
class Message extends DBMapper
{

    protected $_id_message;
    protected $_contenu;


    function __construct ( $id_message )
    {
        //ICI on récupère les informations de l'item
        $requete = "SELECT DISTINCT * FROM message WHERE id_message = :_id_message";
        try {
            $reponse = self::$database->prepare ( $requete );
            $reponse->execute (
                    array (
                            'id_message' => $id_message
                    ) );
        } catch ( PDOException $e ) {
            echo 'Échec lors de la connexion : ' . $e->getMessage ();
        }
        $messageElements = $reponse->fetch ();
        if ( $messageElements == NULL ) {
            throw new Exception( "L'identifiant message :" . $id_message . " est un message inconnu." );
        }
        $this->_id_message = $messageElements[ 'id_message' ];
        $this->_contenu    = $messageElements[ 'contenu' ];
    }

    function afficherMessage ()
    {
        echo '<div class="messages">';
        echo '<p>' . $this->_contenu . '</p>';
        echo '</div>';
    }

    /**
     * @param $id_message
     */
    function getContenu ()
    {
        try {
            $reponse = self::$database->query ( 'SELECT contenu FROM message WHERE idMessage = :_id_message' );
        } catch ( PDOException $e ) {
            echo 'Échec lors de la connexion : ' . $e->getMessage ();
        }

        return $reponse;
    }

    function setContenu ( $nouveauContenu )
    {
        $this->_contenu = $nouveauContenu;
    }
}
