<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );


class ModJoueurVueJoueur
{
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule ()
    {
        echo "<br><div class='profil'><p class='textProfil'>
			<a href='index.php?module=joueur&action=afficher'><button class='buttonModule' style='width:130px;height=20px;'>Afficher Equipes</button></a><br>
			<a href='index.php?module=joueur&action=transferer'><button class='buttonModule' style='width:130px;height=20px;'>Transferer</button></a><br>
			<a href='index.php?module=joueur&action=afficherEquipeOne'><button class='buttonModule' style='width:130px;height=20px;'>Equipe Une</button></a><br>
			<a href='index.php?module=joueur&action=classement'><button class='buttonModule' style='width:130px;height=20px;'>Classement</button></a><br>
			</p></div>";
    }


    /**
     * affiche un joueur : { username ; argent ; personnages }
     *
     * @param $joueur
     */
    static function afficherJoueur ( $joueur )
    {
        echo "Bienvenue " . $joueur[ 'username' ] . " , il vous reste " . $joueur[ 'argent' ] . " gils. <br>";
        //Affichage des différents personnages
        echo '<h2 id="listePerso"> Listes de vos personnages : </h2>';
        ModJoueurVueJoueur::afficherEquipes ( $joueur[ 'equipes' ] );
        /*
        $joueur2=new Joueur();
        $joueur2->getPersonnageWithID(1)->afficherPersonnage();
        $joueur2->getPersonnageWithID(2)->afficherPersonnagePourAttaquer();
        */
    }

    static function afficherEquipes ( $equipes )
    {
        $i = 0;
        foreach ( $equipes as $equipe ) {
            if ( $i == 0 ) {
                echo "<h3>Equipes :</h3>";
            } else {
                echo "<h3>Disponible :</h3>";
            }
            self::afficherEquipe ( $equipe );
            $i++;
        }
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

    static function afficherEquipeOne ( $equipe )
    {
        $equipe->afficherEquipe ();
    }

    static function afficherTransfert ( $joueur )
    {
        //Affichage des différents personnages
        echo '<h2 id="listePerso"> Listes de vos personnages : </h2>';
        $i = 0;
        foreach ( $joueur[ 'equipes' ] as $equipe ) {
            if ( $i == 0 ) {
                echo "<h3>Equipes :</h3>";
            } else {
                echo "<h3>Disponible :</h3>";
            }
            self::afficherPersonnagesTransfert ( $equipe->getPersonnages () );
            $i++;
        }
    }

    static function afficherPersonnagesTransfert ( $personnages )
    {
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
                echo '<td><input type="button" onclick="document.location.href=\'index.php?module=joueur&action=transferer&id_personnage=' . $persoX[ 'id_personnage' ] . '\'" value="' . $persoX[ 'id_personnage' ] . '"/></td>';
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
    }

    static function afficherPersonnagesEtJoueur ( $donneesPersonnages )
    {
        echo "<h2>Classement des Chinelos</h2>";
        echo ' <table border="1" id="tableau">
	<tr>
	    <th> Rang </th>
		<th> Nom Chinelo </th>
		<th> Element </th>
		<th> Niveau </th>
		<th> Exp </th>
		<th> attaques </th>
		<th> HP </th>
        <th> MP </th>
        <th> Puissance </th>
        <th> Defense </th>
        <th> Nom utilisateur </th>
	</tr>';
        $rang = 1;
        foreach ( $donneesPersonnages as $value ) {
            echo "<tr>\n";
            echo "<td>" . $rang . "</td>\n";
            echo "<td>" . $value[ 'nom' ] . "</td>\n";
            echo "<td>" . $value[ 'element' ] . "</td>\n";
            echo "<td>" . $value[ 'niveau' ] . "</td>\n";
            echo "<td>" . $value[ 'experience' ] . "</td>\n";
            echo "<td>" . $value[ 'attaques' ] . "</td>\n";
            echo "<td>" . $value[ 'hp_max' ] . "</td>\n";
            echo "<td>" . $value[ 'mp_max' ] . "</td>\n";
            echo "<td>" . $value[ 'puissance' ] . "</td>\n";
            echo "<td>" . $value[ 'defense' ] . "</td>\n";
            echo "<td>" . $value[ 'username' ] . "</td>";
            echo "</tr>";
            $rang++;
        }
        echo "</table>";
    }
}

