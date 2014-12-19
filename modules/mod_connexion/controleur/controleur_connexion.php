<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );


class ModConnexionControleurConnexion
{

    public function accueilModule ()
    {
        require_once MOD_BPATH . DIR_SEP . "vue/vue_connexion.php";
        ModConnexionVueConnexion::affAccueilModule ();
    }

    public function connexion ()
    {
        require_once MOD_BPATH . DIR_SEP . "modele/modele_connexion.php";
        require_once MOD_BPATH . DIR_SEP . "vue/vue_connexion.php";
        if ( isset ( $_SESSION [ 'id_user' ] ) && $_SESSION[ 'id_user' ] != NULL ) {
            echo 'Vous êtes bien connecté en tant que ' . $_SESSION [ 'username' ];
            $controleur = new ModConnexionControleurConnexion();
            $controleur->deconnexion ();
        } else {
            $donnees = ModConnexionModeleConnexion::connexion ();
            ModConnexionVueConnexion::connexion ( $donnees );
        }
    }

    public function deconnexion ()
    {
        require_once MOD_BPATH . DIR_SEP . "modele/modele_connexion.php";
        require_once MOD_BPATH . DIR_SEP . "vue/vue_connexion.php";
        if ( isset( $_SESSION[ 'id_user' ] ) ) {
            if ( $_SESSION[ 'id_user' ] != NULL ) {
                ModConnexionVueConnexion::deconnexion ();
                if ( isset( $_POST[ 'deconnexion' ] ) ) {
                    if ( $_POST[ 'deconnexion' ] == 'deconnexion' ) {
                        ModConnexionModeleConnexion::deconnexion ();
                        header ( 'Location: index.php' );
                    }
                }
            } else {
                echo 'vous n\'êtes pas connecté.';
            }
        } else {
            echo 'vous n\'êtes pas connecté.';
        }
    }
}
