<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );


class ModContactModeleContact extends DBMapper
{

    public function envoyerMail ($donnees)
    {

         $requete = "SELECT email, username FROM users WHERE username = :username AND id_user = :id_user";
            try {
                $reponse = self::$database->prepare ( $requete );
                $reponse->execute (
                        array (
                                'username' => $_SESSION [ 'username' ],
                                'id_user'  => $_SESSION [ 'id_user' ]
                        ) );
            } catch ( PDOException $e ) {
                echo 'Échec lors de la connexion : ' . $e->getMessage ();
            }
            //Recuperation infos du joueur
            $resultat        = $reponse->fetch ();
            $email  = $resultat[ 'email' ];
            $username = $resultat[ 'username' ];

		
		$objet = $donnees['objet'];
		$header = " FROM :" . $email . " BY :" . $username;
		$message = $donnees['message'];
		$message = wordwrap($message, 70, "\r\n");

		mail('jojax77@hotmail.fr', $objet, $message);
		//mail('jojax77@hotmail.fr', 'HI', 'Marche');

		Header("index.php");
		

    }
	
}