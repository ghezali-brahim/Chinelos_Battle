<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");


class ModCombatVueCombat
{
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule()
    {
        echo "ACCUEIL:";
        echo '<a href="index.php?module=combat&action=afficher">Afficher</a> <br>';
        echo '<a href="index.php?module=combat&action=listeCombat">listeCombat</a> <br>';
        echo '<a href="index.php?module=combat&action=Combat">Combat</a> <br>';
    }

    static function afficherCombat($combat)
    {
        $combat->afficher();
    }

    static function afficherEquipe($equipe)
    {
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
            echo "<td>" . $persoX['id_personnage'] . "</td>";
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

    static function afficherListeCombat($listeEnemies)
    {
        foreach ($listeEnemies as $joueur_IA) {
            //self::afficherEquipe($joueur_IA->getEquipeOne());
            echo $joueur_IA->getEquipeOne()->__toString();
            echo '<br>_____________________________________________________<br>';
        }
    }

    static function afficherTourCombat($combat)
    {

        echo "<br>\n Tour " . $combat->tour . " :";
        $combat->afficher();
        $combat->unTour();
        $combat->afficher();
    }
}

