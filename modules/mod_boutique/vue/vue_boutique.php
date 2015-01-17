<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class ModBoutiqueVueBoutique
{
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule ()
    {
        echo "<div class='boutique'><p class='textBoutique'>
			<a href='index.php?module=boutique&action=afficher'><button class='buttonModule' style='width:130px;height=20px;'>Inventaire</button></a> <br>
			<a href='index.php?module=boutique&action=acheter'><button class='buttonModule' style='width:130px;height=20px;'>Acheter</button></a> <br><br>
		</p></div>";
    }

    static function acheterPersonnage ()
    {
        echo "<div class='aventurierFloat'><a href='index.php?module=boutique&action=acheter&value=personnage'><img class='aventurier' src='include/images/aventurier.jpg'/><br>
		<span>Acheter un personnage</span></a></div>";
    }

    static function formAchatPersonnage ()
    {
        ?>
        <form method="GET" action="index.php?module=boutique&action=acheter&value=personnage" >
            <input type="hidden" name="module" value="boutique" />
            <input type="hidden" name="action" value="acheter" />
            <input type="hidden" name="value" value="personnage" />
            <label for="nom_personnage" >Nom du personnage</label >
            <input type="text" name="nom_personnage" id="id_element" required /><br >
            <label for="id_element" >Element</label >
            <select name="id_element" id="id_element" required >
                <?php
                $elements = Element::getListElements ();
                foreach ( $elements as $element ) {
                    echo "<option value=\"" . $element[ 'id_element' ] . "\"> " . $element[ 'nom' ] . " </option>";
                }
                ?>
            </select >
            <br >
            <input type="reset" value="annuler" />
            <input type="submit" />
        </form >
    <?php
    }

    static function afficherBoutique ()
    {
        //TODO
    }


    static function acheterSoin ()
    {
        echo "<div class='divInfirmiere'><a href='index.php?module=boutique&action=acheter&value=soin'><img class='infirmiere' src='include/images/infirmiere.png'/><br>
		<span>Soigner son Equipe</span></a></div>";
    }

    /**
     * affiche un joueur : { username ; argent ; personnages }
     *
     * @param $joueur
     */
    static function afficherJoueur ( $joueur )
    {
        echo "Bienvenue dans votre inventaire, " . $joueur[ 'username' ] . ".<br><br>";
        echo "<div class='bourse'><p class='textBourse'>" . $joueur[ 'argent' ] . " Gils</p></div><br><br>";
    }
}

