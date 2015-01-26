<?php
/*
 * bd scenario il faut un id_scenario,une description ( en fait c'est le message qui a en clef etrangère le scenario
 * du coup on affiche les messages par ordre croissant de message.id_message selon le scenario en cours
 */


//TODO non fonctionnel
class modele_scenario {

    protected $id_scenario;
    protected $listeMessages;


    function __construct ( $id_scenario ) {
        $requete = "SELECT DISTINCT * FROM message WHERE id_scenario = :id_scenario";
        try {
            $reponse = self::$database->prepare ( $requete );
            $reponse->execute ( array ( 'id_scenario' => $id_scenario ) );
        } catch ( PDOException $e ) {
            echo 'Échec lors de la connexion : ' . $e->getMessage ();
        }
        $messageElements = $reponse->fetch ();
        if ( $messageElements == NULL ) {
            throw new Exception( "L'identifiant scenario :" . $id_scenario . " est un scenario inconnu." );
        }
        $this->listeMessages = array ();
        $this->_contenu      = $messageElements[ 'contenu' ];
    }

    function afficherMessagesScenario () {
        $reponse = self::$database->query ( 'SELECT id_message,contenu FROM message WHERE message.id_scenario=:_id_scenario ORDER BY id_message' );
        while ( $donnees = $reponse->fetch () ) {
            ?>
            <p >
                idMessage : <?php echo $donnees[ 'id_message' ]; ?> <br />
                contenu :<?php echo $donnees[ 'contenu' ]; ?>

            </p >
        <?php
        }
    }
} 