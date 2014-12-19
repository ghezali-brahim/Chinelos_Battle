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
            echo '<a href="index.php?module=connexion&action=connexion">Se connecter</a><br/>';
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
        echo '<form action="index.php?module=connexion&action=deconnexion" method="post">
			<input name="deconnexion" type="submit" onclick="if(!confirm(\'Voulez-vous vraiment vous déconnecter ?\')) return false;" value="deconnexion" />
		</form>';
    }

    static function connexion ( $iduser )
    {
        if ( $iduser == NULL ) {
            ?>
            <form method="POST" action="" >
                <label for="username" >Login :</label >
                <input type="text" id="username" name="username" maxlength="40" size="40" required > <br />
                <label for="password" >Mot de passe :</label >
                <input type="password" id="password" name="password" maxlength="40" size="40" required ><br />
                <input type="submit" name="submit" value="Se connecter" >
            </form >

            <a href="index.php?module=inscription" >S'inscrire</a ><br />
        <?php
        } else {
            self::deconnexion ();
        }
    }
}

?>
