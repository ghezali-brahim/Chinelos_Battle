<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class ModContactVueContact
{
    public static function afficherAccueilContact ()
    {
        echo "Contact<br><br>";
        echo "<div align=center>
			<form method=POST action=index.php?module=contact&action=envoyerMail>
			<input type=hidden name=subject value=formmail>
			<table>
			<tr><td>Objet :</td>
			<td><input type=text name=objet size=30></td></tr>
			<tr><td colspan=2>Votre message:<br>
			<textarea COLS=50 ROWS=6 name=message></textarea>
			</td></tr>
			</table>
			<br> <input type=submit value=Envoyer> -
			<input type=reset value=Annuler>
			</form>
			</div> ";
    }

    public static function formValider ()
    {
    }
}

