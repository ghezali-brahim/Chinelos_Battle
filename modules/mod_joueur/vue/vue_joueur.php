<?php
if (!defined ('TEST_INCLUDE'))
	die ("Vous n'avez pas accès directement à ce fichier");


class ModJoueurVueJoueur
{
	/**
	* Affiche la page d'accueil du module
	**/
	static function affAccueilModule ()
	{
        //TODO
	}

    /** Affiche une liste de personnages
     * @param $personnages
     */
    static function afficherPersonnages($personnages){
        echo ' <table border="1">
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
        <th> ID User </th>
	</tr>';
        foreach ($personnages as $value) {

            $persoX=$value->getPersonnage();
            echo "<tr>\n";
            echo  "<td>".$persoX['id_personnage']."</td>\n";
            echo  "<td>".$persoX['nom']."</td>\n";
            echo  "<td>".$persoX['element']."</td>\n";
            echo  "<td>".$persoX['niveau']."</td>\n";
            echo  "<td>".$persoX['experience']."</td>\n";
            echo  "<td>".$persoX['attaques']."</td>\n";
            echo  "<td>".$persoX['hp']."</td>\n";
            echo  "<td>".$persoX['mp']."</td>\n";
            echo  "<td>".$persoX['puissance']."</td>\n";
            echo  "<td>".$persoX['defense']."</td>\n";
            echo  "<td>".$persoX['id_user']."</td>\n";
            echo "</tr>";
        }

        echo "</table>";
    }

    /** Affichage la liste des personnages des différents participant combattant
     * @param $combat
     */
    static function afficherCombat($combat)
    {
        $combat->afficher();
    }
    /**
     * affiche un joueur : { username ; argent ; personnages }
     * @param $joueur
     */
	static function afficherJoueur($joueur) {
        echo "Bienvenue ". $joueur['username']." , il vous reste ". $joueur['argent']. " gils. <br>";


        //Affichage des différents personnages
        echo '<h2> Listes de vos personnages : </h2>';
        ModJoueurVueJoueur::afficherPersonnages($joueur['personnages']);
	}
}
?>
