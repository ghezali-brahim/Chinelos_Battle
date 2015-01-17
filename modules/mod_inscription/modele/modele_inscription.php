<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class ModInscriptionModeleInscription extends DBMapper
{
    static protected $CONSTANTECRYPT = "iut2015";
    static protected $ARGENT_DEPART  = "10";

    static public function createAccount ( $username, $password, $email )
    {
        if ( !preg_match ( '#^[a-zA-Z0-9_\-]{1,}$#', $username ) ) {
            throw new Exception( "Il y a une erreur lors de la création du compte : $username" );
        }
        if ( !preg_match ( '/^[a-zA-Z0-9_\$\-\.\*]{4,}$/', $password ) ) {
            throw new Exception( "Il y a une erreur lors de la création du compte, mot de passe non valide" );
        }
        if ( !filter_var ( $email, FILTER_VALIDATE_EMAIL ) ) {
            throw new Exception( "Il y a une erreur l'email est invalide $email" );
        }
        $password_sha1 = sha1 ( $username . $password . self::$CONSTANTECRYPT );
        $identifiant   = array ( 'username' => $username, 'password' => $password_sha1, 'email' => $email, 'argent_depart' => self::$ARGENT_DEPART );
        try {
            self::requeteFromDB ( "INSERT INTO users VALUES('', :username, :password, :email, '', '', :argent_depart,'','')", $identifiant );

            return TRUE;
        } catch ( Exception $e ) {
            echo "Echec lors de l'inscription du compte $username, le nom de compte est deja existant\n";

            return FALSE;
        }
    }
}

