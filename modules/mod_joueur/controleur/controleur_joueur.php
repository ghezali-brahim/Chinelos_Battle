<?php
if (!defined ('TEST_INCLUDE'))
	die ("Vous n'avez pas accès directement à ce fichier");

//importation modele
require_once MOD_BPATH."modele".DIR_SEP."modele_attaque.php";
require_once MOD_BPATH."modele".DIR_SEP.'modele_participant.php';
require_once MOD_BPATH."modele".DIR_SEP."modele_joueur.php";
require_once MOD_BPATH."modele".DIR_SEP."modele_joueur_IA.php";
require_once MOD_BPATH."modele".DIR_SEP."modele_personnage.php";
require_once MOD_BPATH."modele".DIR_SEP."modele_combat.php";

//importation vue
require_once MOD_BPATH.DIR_SEP."vue/vue_joueur.php";

class ModJoueurControleurJoueur {
	
	public function accueilModule()
	{
		ModJoueurVueJoueur::affAccueilModule();
	}
	public function afficher(){
        if(!isset($joueur)){
            $joueur = new Joueur();
        }
        ModJoueurVueJoueur::afficherJoueur($joueur->getParticipant());

	}
    public function afficherCombat(){
        if(!isset($joueur)){
            $joueur = new Joueur();
        }
        if(!isset($robot1)){
            $robot1=new Joueur_IA();
        }
        $combat = new Combat($joueur, $robot1);
        ModJoueurVueJoueur::afficherCombat($combat);
        echo $joueur->_toString()."<br>";
        echo $robot1->_toString();
    }


}
?>
