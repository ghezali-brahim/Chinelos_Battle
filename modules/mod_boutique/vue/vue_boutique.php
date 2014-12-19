<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );


class ModBoutiqueVueBoutique
{
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule ()
    {
        echo "ACCUEIL:";
        echo '<a href="index.php?module=boutique&action=afficher">Afficher</a> <br>';
        echo '<a href="index.php?module=boutique&action=acheter">Acheter</a> <br>';
    }

    static function acheterPersonnage ()
    {
        echo '<a href="index.php?module=boutique&action=acheter&value=personnage">Acheter un personnage</a><br/>';
    }

    static function afficherBoutique ()
    {
        //TODO
    }

    static function acheterSoin ()
    {
        echo '<a href="index.php?module=boutique&action=acheter&value=soin">Soigner tous vos personnage</a><br/>';
    }

    /**
     * affiche un joueur : { username ; argent ; personnages }
     *
     * @param $joueur
     */
    static function afficherJoueur ( $joueur )
    {
        echo "Bienvenue dans la boutique, " . $joueur[ 'username' ] . " , il vous reste " . $joueur[ 'argent' ] . " gils. <br>";
    }
}

