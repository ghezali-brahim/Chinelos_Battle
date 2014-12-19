<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );


class Combat extends DBMapper
{
    protected $_joueur;
    protected $_participant_1;
    protected $_participant_2;
    protected $_equipe1;
    protected $_equipe2;
    protected $tour = 1;
    protected $passerTour;

    function __construct ( $participant_1, $participant_2 )
    {
        $this->_joueur        = unserialize ( $_SESSION[ 'joueur' ] );
        $this->_participant_1 = $participant_1;
        $this->_participant_2 = $participant_2;
        $this->_equipe1       = $participant_1->getEquipeOne ();
        $this->_equipe2       = $participant_2->getEquipeOne ();
        $this->passerTour     = FALSE;
    }

    function combattre ()
    {
        if ( $this->unParticipantMort () ) {
            throw new Exception( 'Un joueur a tous ses personnages mort avant même le debut du combat' );
        }
        //echo $this->_equipe1->getPersonnages()[0]->;
        echo "<script>$('#combat').html('" . $this->retourneAffichage () . "')</script>";
        do {
            //TODO il faut mettre un bouton pour passer un Tour (et les tours de chaque personnage)
            echo '<div id="combat"></div>';
            /*
            echo "<br>\n Tour " . $this->tour . " :";
            $this->afficher();
            $this->unTour();
            $this->afficher();
            */
            $this->passerTour ();
            //sleep(1);
            echo "<script>$('#combat').html('" . $this->retourneAffichage () . "')</script>";
        } while ( !$this->unParticipantMort () );
        if ( !( $this->_equipe1->allPersonnagesDead () ) ) {
            $gagnant = $this->_participant_1;
        } else if ( !( $this->_equipe2->allPersonnagesDead () ) ) {
            $gagnant = $this->_participant_2;
        } else {
            throw new Exception( " Il y a une erreur, car le combat est finit mais il ne reste aucun gagnant" );
        }
        if ( get_class ( $gagnant ) == 'Joueur' ) {
            $this->recompenserFinCombat ( $gagnant );
        }
        echo "le joueur gagnant :" . $gagnant->__toString ();
    }

    //TODO
    function unParticipantMort ()
    {
        return ( $this->_equipe1->allPersonnagesDead () || $this->_equipe2->allPersonnagesDead () );
    }

    function retourneAffichage ()
    {
        $text = $this->_equipe1->retourneAffichageEquipe () . "<br/>_________________________________________VS_________________________________________<br/><br/>" . $this->_equipe2->retourneAffichageEquipe ();
        $text = $text . '<a href="index.php?module=combat&action=passerTour">PasserTour</a>';

        return $text;
    }

    function passerTour ()
    {
        $this->passerTour = TRUE;
        $this->unTour ();
        $this->passerTour = FALSE;
    }

    function unTour ()
    {
        $i = 0;
        $j = 0;
        while ( $i < ( $this->_equipe1->getNombrePersonnages () ) || $j < ( $this->_equipe2->getNombrePersonnages () ) ) {
            if ( $i < $this->_equipe1->getNombrePersonnages () ) {
                if ( !$this->_equipe1->getPersonnages ()[ $i ]->isDead () ) {
                    $this->_participant_1->attaquerEnnemi ( $this->_participant_2, $i );
                }
                $i++;
            }
            if ( $j < $this->_equipe2->getNombrePersonnages () ) {
                if ( !$this->_equipe2->getPersonnages ()[ $j ]->isDead () ) {
                    $this->_participant_2->attaquerEnnemi ( $this->_participant_1, $j );
                }
                $j++;
            }
            //MAJ
            $_SESSION[ 'joueur' ] = serialize ( $this->_participant_1 );
        }
        $this->tour++;
    }

    private function recompenserFinCombat ( $gagnant )
    {
        //si participant 1 gagné alors récompense:
        if ( !( $gagnant->getEquipeOne ()->allPersonnagesDead () ) ) {
            // si lvl total inférieur à celui de l'enemi alors + de bonus
            if ( $gagnant->getNiveauTotalParticipant () > $this->_participant_1->getNiveauTotalParticipant () || $gagnant->getNiveauTotalParticipant () > $this->_participant_2->getNiveauTotalParticipant () ) {
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
        }
    }

    function afficher ()
    {
        ModCombatVueCombat::afficherEquipe ( $this->_equipe1 );
        echo '<br>';
        echo '_________________________________________VS_________________________________________';
        echo '<br>';
        ModCombatVueCombat::afficherEquipe ( $this->_equipe2 );
    }

    function afficher2 ()
    {
        echo 'Niveau total: ' . $this->_equipe1->getNiveauTotalPersos ();
        ModCombatVueCombat::afficherEquipe ( $this->_equipe1 );
        echo '<br><br>';
        echo '_________________________________________VS_________________________________________';
        echo '<br><br>';
        echo 'Niveau total: ' . $this->_equipe2->getNiveauTotalPersos ();
        ModCombatVueCombat::afficherEquipe ( $this->_equipe2 );
    }
}