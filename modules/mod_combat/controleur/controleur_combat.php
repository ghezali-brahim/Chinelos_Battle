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
    protected $_combat;
    protected $_joueur;
    protected $_ennemi;

    function __construct ()
    {
        if ( Joueur::connectee () ) {
            if ( !isset( $_SESSION[ 'joueur' ] ) ) {
                $_SESSION[ 'joueur' ] = serialize ( new Joueur() );
            }
        }
        $this->_joueur = unserialize ( $_SESSION[ 'joueur' ] );
    }

    public function accueilModule ()
    {
        ModCombatVueCombat::affAccueilModule ();
    }


    public function afficher ()
    {
        //TODO
    }

    public function listeCombat ()
    {
        $this->_joueur = unserialize ( $_SESSION[ 'joueur' ] );
        $listeEnemies  = array ();
        for ( $i = 0; $i < 10; $i++ ) {
            if ( $i < 5 ) {
                array_push ( $listeEnemies, new Joueur_IA( $this->_joueur->getNiveauTotalParticipant () - $i ) );
            } else {
                array_push ( $listeEnemies, new Joueur_IA( $this->_joueur->getNiveauTotalParticipant () + ( $i - 5 ) ) );
            }
        }
        ModCombatVueCombat::afficherListeCombat ( $listeEnemies );
    }

    public function afficherTour ()
    {
        ModCombatVueCombat::afficherTourCombat ( $this->_combat );
    }

    public function affichageUnTour ()
    {
        $this->_joueur->incrementerIndicePersoActuelParticipant();
        ModCombatVueCombat::affichageUnTour ( $this->_joueur );
    }
    //TODO continuer le combat
    public function attaquePersonnage(){
        $indicePersoEnnemi=$_POST['indice_ennemi'];
        $indiceAttaqueChoisit=$_POST['indice_attaque_choisit'];
        $this->_joueur->getPersoIndiceActuel()->setIndiceAttaqueChoisit($indiceAttaqueChoisit);
        $this->_joueur->getPersoIndiceActuel()->attaquerPersonnage($this->_ennemi->getEquipeOne()->getPersonnageIndice($indicePersoEnnemi));
    }
    public function combat ()
    {
        $this->_joueur = unserialize ( $_SESSION[ 'joueur' ] );
        if ( !isset( $robot1 ) ) {
            $robot1 = new Joueur_IA( $this->_joueur->getNiveauTotalParticipant () );
        }
        $this->_joueur->virerMortEquipeOne ();
        $robot1->virerMortEquipeOne ();
        try {
            $this->_combat = new Combat( $this->_joueur, $robot1 );
            $this->_combat->combattre ();
            $_SESSION[ 'joueur' ] = serialize ( $this->_joueur );
        } catch ( Exception $e ) {
            if ( $e->getMessage () == "Votre Equipe Une n'a plus de personnage apte à combattre !" ) {
                echo " <br>Votre Equipe Une n'a plus de personnage apte à combattre !<br>
					<br> Merci de vous dirigez vers la <a href='index.php?module=boutique'>Boutique</a> pour les soigner !";
            }
        }
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
        $this->_combat->passerTour ();
    }
}

