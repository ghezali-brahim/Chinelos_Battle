<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "modele/modele_connexion.php";
require_once MOD_BPATH . DIR_SEP . "vue/vue_connexion.php";


class ModConnexionControleurConnexion
{

    public function connexion ()
    {
        if ( isset( $_SESSION[ 'user' ] ) ) {
            header ( "Refresh: 0;URL=index.php" );
            die();
        }
        if ( isset( $_POST[ 'username' ] ) && isset( $_POST[ 'password' ] ) ) {
            $username = $_POST[ 'username' ];
            $password = $_POST[ 'password' ];
            unset( $_POST[ 'username' ] );
            unset( $_POST[ 'password' ] );
            if ( preg_match ( '/^[a-zA-Z0-9]{4,}$/', $username ) ) {
                if ( preg_match ( '/^[a-zA-Z0-9_\$\-\.\*]{4,}$/', $password ) ) {
                    try {
                        $_SESSION[ 'user' ] = serialize ( new ModConnexionModeleConnexion( $username, $password ) );
                        echo "Connexion reussit ! ";
                        header ( "Refresh: 2;URL=index.php" );
                    } catch ( Exception $e ) {
                        self::accueilModule ();
                        echo $e->getMessage ();
                    }
                } else {
                    self::accueilModule ();
                    echo "not valid password, alphanumeric & longer than or equals 4 chars\n";
                    echo "caractere autorisé : alpha-numeric et _ - $ * .";
                }
            } else {
                self::accueilModule ();
                echo "not valid username, alphanumeric & longer than or equals 4 chars";
            }
        } else {
            self::accueilModule ();
        }
    }

    public function accueilModule ()
    {
        if ( isset( $_SESSION[ 'user' ] ) ) {
            $user = unserialize ( $_SESSION[ 'user' ] );
            if ( $user->connectedOrNot () ) {
                ModConnexionVueConnexion::formDeconnexion ();
            } else {
                ModConnexionVueConnexion::formConnexion ();
            }
        } else {
            ModConnexionVueConnexion::formConnexion ();
        }
    }

    public function deconnexion ()
    {
        $user = unserialize ( $_SESSION[ 'user' ] );
        $user->deconnection ();
        unset( $_SESSION[ 'user' ] );
        unset( $user );
        header ( "Refresh: 0;URL=index.php" );
        die();
    }
}
