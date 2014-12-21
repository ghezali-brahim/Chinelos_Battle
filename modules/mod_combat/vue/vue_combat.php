<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );


class ModCombatVueCombat
{
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule ()
    {
        echo "<br><div class='combat'><p class='textCombat'>
			<a href='index.php?module=combat&action=listeCombat'><button class='buttonModule' style='width:130px;height=20px;'>Scénario</button></a><br>
			<a href='index.php?module=combat&action=Combat'><button class='buttonModule' style='width:130px;height=20px;'>Combat Aléatoire</button></a><br>
			<a href='index.php?module=combat&action=unTour'><button class='buttonModule' style='width:130px;height=20px;'>Un tour</button></a><br>
			</p></div>";
    }

    static function afficherCombat ( $combat )
    {
        $combat->afficher ();
    }

    static function afficherEquipe ( $equipe )
    {
        self::afficherPersonnages ( $equipe->getPersonnages () );
    }

    /** Affiche une liste de personnages
     *
     * @param $personnages
     */
    static function afficherPersonnages ( $personnages )
    {
        echo ' <table border="1" id="tableau">
	<tr>
		<th> ID Perso </th>
		<th> Nom </th>
		<th> Element </th>
		<th> Niveau </th>
		<th> Exp </th>
		<th> attaques </th>
		<th> HP </th>
        <th> MP </th>
        <th> Puissance </th>
        <th> Defense </th>
        <th> ID Equipe </th>
	</tr>';
        foreach ( $personnages as $value ) {
            $persoX = $value->getPersonnage ();
            echo "<tr>\n";
            echo "<td>" . $persoX[ 'id_personnage' ] . "</td>";
            echo "<td>" . $persoX[ 'nom' ] . "</td>\n";
            echo "<td>" . $persoX[ 'element' ] . "</td>\n";
            echo "<td>" . $persoX[ 'niveau' ] . "</td>\n";
            echo "<td>" . $persoX[ 'experience' ] . "</td>\n";
            echo "<td>" . $value->getIdAttaques () . "</td>\n";
            echo "<td>" . $persoX[ 'hp' ] . "</td>\n";
            echo "<td>" . $persoX[ 'mp' ] . "</td>\n";
            echo "<td>" . $persoX[ 'puissance' ] . "</td>\n";
            echo "<td>" . $persoX[ 'defense' ] . "</td>\n";
            echo "<td>" . $persoX[ 'id_equipe' ] . "</td>\n";
            echo "</tr>";
        }
        echo "</table>";
    }

    static function afficherListeCombat ( $listeEnemies )
    {
        foreach ( $listeEnemies as $joueur_IA ) {
            //self::afficherEquipe($joueur_IA->getEquipeOne());
            //echo $joueur_IA->getEquipeOne ()->__toString ();
            echo "<br>";
            echo $joueur_IA->retourneAffichageJoueurIA ();
            echo "<br><button class='affronterEquipe'>Affronter cette équipe !</button>";
            echo "<br><br><img src='include/images/sep.gif'/><br>";
        }
    }

    static function afficherTourCombat ( $combat )
    {
        echo "<br>\n Tour " . $combat->tour . " :";
        $combat->afficher ();
        $combat->unTour ();
        $combat->afficher ();
    }

    static function affichageUnTour ( $joueur )
    {
        echo $joueur->getEquipeOne ()->retourneAffichageEquipeAvecAttaques ();
        $ennemi           = Equipe::createEquipe ( 5, NULL );
        $personnageActuel = $joueur->getEquipeOne ()->getPersoIndiceActuel ();

        //TODO à voir
        ?>
        <form method="POST" action="" >
            <label for="indice_ennemi" >Indice ennemi &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label >
            <input type="number" id="indice_ennemi" name="indice_ennemi" min="0"
                   max="<?php echo ( $ennemi->getNombrePersonnages () - 1 ); ?>" required /> <br ><br >
            <label for="indice_attaque" >Indice attaque :</label >
            <input type="number" id="indice_attaque" name="indice_attaque" min="0"
                   max="<?php echo ( $personnageActuel->getNombreAttaque () - 1 ); ?>" required /><br ><br ><br >
            <input type="submit" name="submit" value="Attaquer" >
        </form >
        <?php
        echo $ennemi->retourneAffichageEquipe ();
    }
}

