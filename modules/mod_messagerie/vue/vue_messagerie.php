<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class ModMessagerieVueMessagerie
{
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule ()
    {
        echo "<br><div class='messagerie'><p>
			<a href='index.php?module=messagerie&action=afficher'><button class='buttonModule' style='width:130px;height=20px;'>Afficher</button></a><br>
			<a href='index.php?module=messagerie&action=envoyer_message'><button class='buttonModule' style='width:130px;height=20px;'>Envoyer message</button></a><br>
			</p></div>";
    }

    static function afficherlistesMessages ( $messages )
    {
        if ( count ( $messages ) == 1 ) {
            static::afficherMessage ( $messages );
        } else {
            foreach ( $messages as $message ) {
                static::afficherMessage ( $message );
                echo "<br><br>";
            }
        }
    }

    static function afficherMessage ( $message )
    {
        $contenuMessage = $message->getMessagerieArray ();
        print_r ( $contenuMessage );
    }

    static function afficherFormEnvoieMessage ()
    {
        ?>
        <form action="" method="POST" >
            <label for="objet" >Objet:</label >
            <input type="text" name="objet" id="objet" required />
            <label for="contenu" >Contenu:</label >
            <input type="text" name="contenu id=" contenu" required />
            <input type="submit" />
        </form >
    <?php
    }
}

