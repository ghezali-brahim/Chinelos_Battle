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
		echo "<div class='boutique'><p class='textBoutique'>
			<a href='index.php?module=boutique&action=afficher'><button class='buttonModule' style='width:130px;height=20px;'>Inventaire</button></a> </br>
			<a href='index.php?module=boutique&action=acheter'><button class='buttonModule' style='width:130px;height=20px;'>Acheter</button></a> </br></br>
		</p></div>";
    }

    static function acheterPersonnage ()
    {		
		echo "<div class='aventurierFloat'><a href='index.php?module=boutique&action=acheter&value=personnage'><img class='aventurier' src='include/images/aventurier.jpg'/></br>
		<span>Acheter un personnage</span></a></div>";
    }

    static function afficherBoutique ()
    {
        //TODO
    }
	
	

    static function acheterSoin ()
    {
		echo "<div class='divInfirmiere'><a href='index.php?module=boutique&action=acheter&value=soin'><img class='infirmiere' src='include/images/infirmiere.png'/></br>
		<span>Soigner son Equipe</span></a></div>";
    }

    /**
     * affiche un joueur : { username ; argent ; personnages }
     *
     * @param $joueur
     */
    static function afficherJoueur ( $joueur )
    {
		
        echo "Bienvenue dans votre inventaire, " . $joueur[ 'username' ] . ".</br></br>";
		echo "<div class='bourse'><p class='textBourse'>" . $joueur[ 'argent' ] . " Gils</p></div></br></br>";
		
    }
}

