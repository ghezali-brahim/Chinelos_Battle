<?php
/**
 * Created by PhpStorm.
 * User: Caporal
 * Date: 28/10/2014
 * Time: 22:08
 */
require_once "mod_joueur/modele/modele_attaque.php";
require_once "mod_joueur/modele/modele_niveau.php";
require_once "mod_joueur/modele/modele_participant.php";
require_once "mod_joueur/modele/modele_joueur.php";
require_once "mod_joueur/modele/modele_joueur_IA.php";
require_once "mod_joueur/modele/modele_personnage.php";
require_once "mod_joueur/modele/modele_equipe.php";
require_once "mod_joueur/modele/modele_combat.php";

class joueurTest extends PHPUnit_Framework_TestCase {

    public function testPushAndPop()
    {
        $joueur= new Joueur_IA(1);

        $this->assertEquals(1,  $joueur->getEquipeOne()->getNombrePersonnages());
    }

}
 