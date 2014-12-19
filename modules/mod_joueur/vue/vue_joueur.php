<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");


class ModJoueurVueJoueur
{
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule()
    {
        //TODO
    }

    /** Affichage la liste des personnages des différents participant combattant
     *
     * @param $combat
     */
    static function afficherCombat($combat)
    {
        $combat->afficher();
    }

    /**
     * affiche un joueur : { username ; argent ; personnages }
     *
     * @param $joueur
     */
    static function afficherJoueur($joueur)
    {
        echo "Bienvenue " . $joueur['username'] . " , il vous reste " . $joueur['argent'] . " gils. <br>";


        //Affichage des différents personnages
        echo '<h2 id="listePerso"> Listes de vos personnages : </h2>';

        ModJoueurVueJoueur::afficherEquipes($joueur['equipes']);
    }

    static function afficherEquipes($equipes){

	$i=0;
        foreach($equipes as $equipe){
		if($i==0){
			echo "<h3><u> Equipes :</u></h3>";
		}else{
			echo "<h3><u> Disponible :</u></h3>";
		}
            self::afficherEquipe($equipe);
		$i++;
        }
    }
    static function afficherEquipe($equipe){
        self::afficherPersonnages($equipe->getPersonnages());
    }
    /** Affiche une liste de personnages
     *
     * @param $personnages
     */
    static function afficherPersonnages($personnages)
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

        foreach ($personnages as $value) {
	
        $persoX = $value->getPersonnage();
        echo "<tr>\n";
	echo '<td><input type="button" onclick="javascript:document.location.href=\'index.php?module=joueur&action=transferer&id_personnage='.$persoX['id_personnage'].'\'" value="'.$persoX['id_personnage'].'"/></td>';
        echo "<td>" . $persoX['nom'] . "</td>\n";
        echo "<td>" . $persoX['element'] . "</td>\n";
        echo "<td>" . $persoX['niveau'] . "</td>\n";
        echo "<td>" . $persoX['experience'] . "</td>\n";
        echo "<td>" . $value->getIdAttaques() . "</td>\n";
        echo "<td>" . $persoX['hp'] . "</td>\n";
        echo "<td>" . $persoX['mp'] . "</td>\n";
        echo "<td>" . $persoX['puissance'] . "</td>\n";
        echo "<td>" . $persoX['defense'] . "</td>\n";
        echo "<td>" . $persoX['id_equipe'] . "</td>\n";
        echo "</tr>";
        }

        echo "</table>";
    }


}

