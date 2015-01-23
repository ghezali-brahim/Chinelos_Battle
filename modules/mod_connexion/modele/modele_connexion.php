<?php
if ( ! defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class ModConnexionModeleConnexion extends DBMapper {
    static protected $CONSTANTECRYPT = "iut2015";
    protected        $_id_user;
    protected        $_username;
    protected        $_email;
    protected        $_connected;
    protected        $_last_connection;

    function __construct ( $username, $password ) {
        $this->_username = $username;
        $password        = $this->sha1_encrypt ( $password );
        $donnees         = array ( 'username' => $username, 'password' => $password );
        $resultat        = static::requeteFromDB ( "select id_user, username, email, last_connection from users where username=:username AND password=:password", $donnees );
        if ( count ( $resultat ) == 0 ) {
            throw new Exception( "Echec de la connexion : username ou password incorrect !" );
        } else {
            $last_date = date_create ()->format ( 'Y-m-d  H:i:s' );
            $resultat  = $resultat[ 0 ];
            //Definition variable
            $this->_last_connection = $last_date;
            $this->_id_user         = $resultat[ 'id_user' ];
            $this->_email           = $resultat[ 'email' ];
            $this->_connected       = TRUE;
            $donnees                = array ( 'last_connection' => $this->_last_connection, 'id_user' => $this->_id_user, 'connected' => TRUE );
            static::requeteFromDB ( "UPDATE users SET last_connection = :last_connection, connected = :connected WHERE id_user=:id_user", $donnees );
        }
    }

    function sha1_encrypt ( $mot ) {
        return sha1 ( $this->_username . $mot . self::$CONSTANTECRYPT );
    }

    function sha1_compare ( $mot, $sha1 ) {
        return $this->sha1_encrypt ( $mot ) == $sha1;
    }

    function deconnection () {
        $this->_connected = FALSE;
        static::requeteFromDB ( "UPDATE users SET connected = :connected WHERE id_user=:id_user", array ( 'connected' => $this->_connected, 'id_user' => $this->_id_user ) );
    }

    function connectedOrNot () {
        return $this->_connected;
    }

    /**
     * @return integer
     */
    public function getIdUser () {
        return $this->_id_user;
    }


    /**
     * @return varchar
     */
    public function getUsername () {
        return $this->_username;
    }

    public function getUsersConnected () {
        return self::requeteFromDB ( "select username from users where connected=TRUE" );
    }
}

