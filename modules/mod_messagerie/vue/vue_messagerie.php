<?php
if ( ! defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class ModMessagerieVueMessagerie {
    /**
     * Affiche la page d'accueil du module
     **/
    static function affAccueilModule () {
        echo "<br><div class='messagerie'><p>
			<a href='index.php?module=messagerie&action=afficher'><button class='buttonModule' style='width:130px;height=20px;'>Afficher</button></a><br>
			<a href='index.php?module=messagerie&action=envoyer_message'><button class='buttonModule' style='width:130px;height=20px;'>Envoyer message</button></a><br>
			</p></div>";
    }

    static function afficherlistesMessages ( $messageEnvoyer, $messageRecus ) {
		echo ' <h3>Boite de reception</h3>';
        echo ' <table border="1" id="tableau">
	<tr>
		<th> Destinataire </th>
		<th> Objet </th>
		<th> Contenu </th>
		<th> Date </th>
	</tr>';
        foreach ( $messageEnvoyer as $message ) {
            echo "<tr>\n";
			echo "<td style='width:160px'>" . $message[ 'username_destinataire' ] . "</td>\n";
            echo "<td style='width:200px'>" . $message[ 'objet' ] . "</td>\n";
            echo "<td style='width:600px'>" . $message[ 'contenu' ] . "</td>\n";
			echo "<td style='width:150px'>" . $message[ 'date_envoie' ] . "</td>\n";
            echo "</tr>";
        }
		
        echo "</table>";
		
		
		echo " <h3>Boite d'envoi</h3>";
		echo ' <table border="1" id="tableau">
	<tr>
		<th> Expediteur </th>
		<th> Objet </th>
		<th> Contenu </th>
		<th> Date </th>
	</tr>';
        foreach ( $messageRecus as $message ) {
            echo "<tr>\n";
			echo "<td style='width:160px'>" . $message[ 'username_expeditaire' ] . "</td>\n";
            echo "<td style='width:200px'>" . $message[ 'objet' ] . "</td>\n";
            echo "<td style='width:600px'>" . $message[ 'contenu' ] . "</td>\n";
			echo "<td style='width:150px'>" . $message[ 'date_envoie' ] . "</td>\n";
            echo "</tr>";
        }
        echo "</table>";
    }

    static function afficherMessage ( $message ) {
        $contenuMessage = $message->getMessagerieArray ();
        print_r ( $contenuMessage );
    }

    static function afficherFormEnvoieMessage () {
        ?>
		<br>
        <form action="" method="POST" >
            <label for="objet" >Objet : </label >
            <input style="width:400px; margin-left:18px" type="text" name="objet" id="objet" required />
			<br><br>
			<label for="destinataire" >Destinataire : </label >
			<select name="destinataire" id="destinataire" style="width:400px; margin-right:24px">
			<?php
				$users=Joueur::getListesUsers(); 
				foreach($users as $user){
					 echo "<option value=\"" . $user[ 'id_user' ] . "\"> " . $user[ 'username' ] . " </option>" ;
				}
			?>
			</select>
         <!--  <input style="width:400px; margin-right:24px" type="text" name="destinataire" id="destinataire" required />-->
			<br><br>
            <label for="contenu" >Contenu :</label >
            <input style="width:400px; height:200px" type="text" name="contenu" id="contenu" required />
			<br><br>
            <input type="submit" value="Envoyer"/>
        </form >
    <?php
    }
}

