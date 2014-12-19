<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once "modules/include_objects.php";
//importation modele
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_combat.php";
require_once MOD_BPATH . "modele" . DIR_SEP . "modele_game.php";
//importation vue
require_once MOD_BPATH . DIR_SEP . "vue/vue_combat.php";


class ModCombatControleurCombat
{
    protected $combat;

    public function accueilModule ()
    {
        ModCombatVueCombat::affAccueilModule ();
    }


    public function afficher ()
    {
        if ( !isset( $joueur ) ) {
            $joueur = new Joueur();
        }
        if ( !isset( $robot1 ) ) {
            $robot1 = new Joueur_IA( $joueur->getNiveauTotalParticipant () );
        }
        if ( !isset( $robot2 ) ) {
            $robot2 = new Joueur_IA( $joueur->getNiveauTotalParticipant () );
        }
        $combat = new Combat( $joueur, $robot1 );
        ModCombatVueCombat::afficherCombat ( $combat );
        //        $robot1->getPersonnages()[0]->attaque()
        echo $joueur->__toString () . "<br>";
        echo '<br>';
        echo $robot1->__toString ();
        echo '<br>';
        echo $robot2->__toString ();
        //$combat->combattre();
        //$degatJoueur1=$joueur->getEquipeOne()->getPersonnages()[0]->attaquer(0);
        //$robot1->getEquipeOne()->getPersonnages()[0]->subirDegats($degatJoueur1);
        //ModJoueurVueJoueur::afficherCombat($combat);
    }

    public function listeCombat ()
    {
        $joueur       = new Joueur();
        $listeEnemies = array ();
        for ( $i = 0; $i < 10; $i++ ) {
            if ( $i < 5 ) {
                array_push ( $listeEnemies, new Joueur_IA( $joueur->getNiveauTotalParticipant () - $i ) );
            } else {
                array_push ( $listeEnemies, new Joueur_IA( $joueur->getNiveauTotalParticipant () + ( $i - 5 ) ) );
            }
        }
        ModCombatVueCombat::afficherListeCombat ( $listeEnemies );
    }

    public function afficherTour ()
    {
        ModCombatVueCombat::afficherTourCombat ( $this->combat );
    }

    public function combat ()
    {
        if ( !isset( $joueur ) ) {
            $joueur = new Joueur();
        }
        if ( !isset( $robot1 ) ) {
            $robot1 = new Joueur_IA( $joueur->getNiveauTotalParticipant () );
        }
        $joueur->virerMortEquipeOne ();
        $robot1->virerMortEquipeOne ();
        $this->combat = new Combat( $joueur, $robot1 );
        $this->combat->combattre ();
        /*
        $personnage1=$joueur->getEquipeOne()->getPersonnages()[0];
        $monstre1= $robot1->getEquipeOne()->getPersonnages()[0];

        //personnage1 attaque monstre1
        $personnage1->afficherPersonnagePourAttaquer();
        $monstre1->afficherPersonnage();
        $degatsAinfligers=$personnage1->attaquer(2);
        $monstre1->subirDegats($degatsAinfligers);
        $personnage1->afficherPersonnagePourAttaquer();
        $monstre1->afficherPersonnage();
        echo '________________________________________________';
        //monstre1 attaque personnage1
        $personnage1->afficherPersonnagePourAttaquer();
        $monstre1->afficherPersonnage();
        $degatsAinfligers=$monstre1->attaquer(1);
        $personnage1->subirDegats($degatsAinfligers);
        $personnage1->afficherPersonnagePourAttaquer();
        $monstre1->afficherPersonnage();
        */
    }

    public function passerTour ()
    {
        $this->combat->passerTour ();
    }
}

