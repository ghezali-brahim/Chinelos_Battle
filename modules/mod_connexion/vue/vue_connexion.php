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
        if ( !isset( $_SESSION[ 'id_user' ] ) ) {
            echo '<br><a href="index.php?module=connexion&action=connexion"><button>Se connecter</button></a><br><br>';
        } else {
            if ( $_SESSION[ 'id_user' ] != NULL ) {
                self::deconnexion ();
            } else {
                echo '<a href="index.php?module=connexion&action=connexion">Se connecter</a><br/>';
            }
        }
    }

    static function deconnexion ()
    {
        echo '<br><form action="index.php?module=connexion&action=deconnexion" method="post">
			<input name="deconnexion" type="submit" onclick="if(!confirm(\'Voulez-vous vraiment vous déconnecter ?\')) return false;" value="deconnexion" />
		</form><br>';
    }

    static function afficherBouton ()
    {
        if ( isset( $_SESSION[ 'id_user' ] ) ) {
            if ( $_SESSION[ 'id_user' ] == NULL ) {
                echo '<a href="index.php?module=connexion&action=connexion"><button>Se Connecter</button></a>';
            } else {
                echo '<a href="index.php?module=connexion&action=deconnexion"><button>Se Deconnecter</button></a>';
            }
        } else {
            echo '<a href="index.php?module=connexion&action=connexion"><button>Se Connecter</button></a>';
        }
    }

    static function connexion ( $iduser )
    {
        if ( $iduser == NULL ) {
            ?>
            <br >
            <form method="POST" action="" >
                <label for="username" >Login
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label >
                <input type="text" id="username" name="username" maxlength="40" size="40" required > <br ><br >
                <label for="password" >Mot de passe :</label >
                <input type="password" id="password" name="password" maxlength="40" size="40" required ><br ><br ><br >
                <input type="submit" name="submit" value="Se connecter" >
            </form >
            <br >
            <a href="index.php?module=inscription" >S'inscrire</a ><br />
            <br >
        <?php
        } else {
            self::deconnexion ();
            header ( 'Location: index.php' );
        }
    }
}


?>