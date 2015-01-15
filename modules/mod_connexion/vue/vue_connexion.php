<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );


class ModConnexionVueConnexion
{
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule ()
    {
    }

    static function formConnexion ()
    {
        ?>
        <form method="POST" action="index.php?module=connexion&action=connexion" >
            <label for="username" >Login :</label >
            <input type="text" id="username" name="username" maxlength="9" size="9" required > <br ><br >
            <label for="password" >Mot de passe :</label >
            <input type="password" id="password" name="password" maxlength="15" size="15" required ><br ><br ><br >
            <input type="submit" name="submit" value="Se connecter" >
        </form >
        <br >
        <a href="index.php?module=inscription" >S'inscrire</a ><br />
        <br >
    <?php
    }

    static function formDeconnexion ()
    {
        self::afficherBouton ();
    }

    static function afficherBouton ()
    {
        if ( isset( $_SESSION[ 'user' ] ) ) {
            if ( $_SESSION[ 'user' ] == NULL ) {
                echo '<a href="index.php?module=connexion&action=connexion"><button>Se Connecter</button></a>';
            } else {
                echo '<a href="index.php?module=connexion&action=deconnexion"><button>Se Deconnecter</button></a>';
            }
        } else {
            echo '<a href="index.php?module=connexion&action=connexion"><button>Se Connecter</button></a>';
        }
    }
}


?>