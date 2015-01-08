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
        if ( !isset( $_SESSION[ 'joueur' ] ) ) {
            $_SESSION[ 'joueur' ] = serialize ( new Joueur() );
        }
        $this->_joueur = unserialize ( $_SESSION[ 'joueur' ] );
        if ( !isset( $_SESSION[ 'ennemi' ] ) ) {
            $_SESSION[ 'ennemi' ] = serialize ( new Joueur_IA( $this->_joueur->getNiveauTotalParticipant () ) );
        }
        $this->_ennemi = unserialize ( $_SESSION[ 'ennemi' ] );
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
        $_SESSION[ 'listeEnnemies' ] = serialize ( $listeEnemies );
        ModCombatVueCombat::afficherListeCombat ( $listeEnemies );
    }

    public function afficherTour ()
    {
        ModCombatVueCombat::afficherTourCombat ( $this->_combat );
    }

    public function affichageUnTour ()
    {
        if ( isset( $_POST[ 'ennemi_choisit' ] ) ) {
            $liste_ennemies       = unserialize ( $_SESSION[ 'listeEnnemies' ] );
            $this->_ennemi        = $liste_ennemies[ $_POST[ 'ennemi_choisit' ] ];
            $_SESSION[ 'ennemi' ] = serialize ( $this->_ennemi );
        }
        if ( $this->_ennemi->getEquipeOne ()->allPersonnagesDead () == TRUE && $this->_joueur->getEquipeOne ()->allPersonnagesDead () == FALSE ) {
            $this->recompenserFinCombat ( $this->_joueur, $this->_ennemi );
            $_SESSION[ 'joueur' ] = serialize ( $this->_joueur );
            $_SESSION[ 'ennemi' ] = serialize ( new Joueur_IA( $this->_joueur->getNiveauTotalParticipant () ) );
            $this->_ennemi        = $this->_ennemi = unserialize ( $_SESSION[ 'ennemi' ] );
            header ( "Refresh: 4;URL=index.php?module=combat" );
        }
        if ( $this->_ennemi->getEquipeOne ()->allPersonnagesDead () == FALSE && $this->_joueur->getEquipeOne ()->allPersonnagesDead () == TRUE ) {
            $_SESSION[ 'joueur' ] = serialize ( $this->_joueur );
            $_SESSION[ 'ennemi' ] = serialize ( new Joueur_IA( $this->_joueur->getNiveauTotalParticipant () ) );
            $this->_ennemi        = $this->_ennemi = unserialize ( $_SESSION[ 'ennemi' ] );
            echo "Vous avez perdu";
            header ( "Refresh: 4;URL=index.php?module=boutique&action=acheter" );
        }
        $this->_joueur->incrementerIndicePersoActuelParticipant ();
        $this->_ennemi->incrementerIndicePersoActuelParticipant ();
        ModCombatVueCombat::affichageUnTour ( $this->_joueur, $this->_ennemi );
        //echo "<script>$('#combat').html('" . ModCombatVueCombat::retourneAffichageTour ( $this->_joueur , $this->_ennemi)."')</script>";
        if ( isset( $_GET[ 'attaquePersonnage' ] ) ) {
            if ( $_GET[ 'attaquePersonnage' ] == TRUE ) {
                try {
                    $this->attaquePersonnage ();
                } catch ( Exception $e ) {
                    echo "Impossible d'attaquer avec ce personnage" . $this->_joueur->getPersoIndiceActuel ()->__toString ();
                }
            }
        }
        $this->_ennemi->attaquer ( $this->_joueur );
        $_SESSION[ 'joueur' ] = serialize ( $this->_joueur );
        $_SESSION[ 'ennemi' ] = serialize ( $this->_ennemi );
    }

    //TODO continuer le combat
    private function recompenserFinCombat ( $gagnant, $perdant )
    {
        //si participant 1 gagné alors récompense:
        if ( !( $gagnant->getEquipeOne ()->allPersonnagesDead () ) ) {
            // si lvl total inférieur à celui de l'enemi alors + de bonus
            if ( $gagnant->getNiveauTotalParticipant () < $perdant->getNiveauTotalParticipant () ) {
                //ICI recompense sous la forme (argent, %xp)
                $recompense_p1 = array (
                        'argent'     => 10,
                        'pourcentXP' => 6 );
            } else {
                $recompense_p1 = array (
                        'argent'     => 5,
                        'pourcentXP' => 4 );
            }
            echo "votre récompense de fin de combat est : " . $recompense_p1[ 'argent' ] . "gils ainsi que " . $recompense_p1[ 'pourcentXP' ] . "% d'experience. <br/>";
            $gagnant->ajouterArgent ( $recompense_p1[ 'argent' ] );
            $gagnant->ajouterPourcentExperience ( $recompense_p1[ 'pourcentXP' ] );
            //$_SESSION[ 'ennemi' ] = serialize ( new Joueur_IA( $this->_joueur->getNiveauTotalParticipant () ) );
            unset( $_SESSION[ 'ennemi' ] );
        }
    }

    public function attaquePersonnage ()
    {
        $indicePersoEnnemi    = $_POST[ 'indice_ennemi' ];
        $indiceAttaqueChoisit = $_POST[ 'indice_attaque' ];
        $this->_joueur->getPersoIndiceActuel ()->setIndiceAttaqueChoisit ( $indiceAttaqueChoisit );
        $this->_joueur->getPersoIndiceActuel ()->attaquerPersonnage ( $this->_ennemi->getEquipeOne ()->getPersonnageIndice ( $indicePersoEnnemi ) );
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

