<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class ModInscriptionVueInscription
{
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule ()
    {
        ?>
        <form method="post" action="index.php?module=inscription&action=inscription" >
            <label >Login: <input type="text" name="username" required /></label ><br />
            <label >Mot de passe: <input type="password" name="password" required /></label ><br />
            <label >Confirmation du mot de passe: <input type="password" name="password2" required /></label ><br />
            <label >Adresse e-mail: <input type="email" name="email" required /></label ><br />
            <input type="reset" value="annuler" />
            <input type="submit" value="inscription" name="module" />
        </form >


    <?php
    }

    static function inscription ( $reussit )
    {
        if ( $reussit == 1 ) {
            echo 'inscription reussie';
            header ( "Refresh: 5;URL=index.php" );
            exit();
        } else {
            echo 'inscription échoué';
            header ( "Refresh: 5;URL=index.php?module=inscription" );
            exit();
        }
        //var_dump($vins);
        //print_r($vins);
    }
}

?>
