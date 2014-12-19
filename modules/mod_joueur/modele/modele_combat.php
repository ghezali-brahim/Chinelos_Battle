<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

class Combat extends DBMapper
{

    protected $_participant_1;
    protected $_participant_2;
    protected $_equipe1;
    protected $_equipe2;
    protected $tour = 1;

    function __construct($participant_1, $participant_2)
    {
        $this->_participant_1 = $participant_1;
        $this->_participant_2 = $participant_2;
        $this->_equipe1       = $participant_1->getEquipeOne();
        $this->_equipe2       = $participant_2->getEquipeOne();
    }

    function afficher()
    {
        echo 'Niveau total: ' . $this->_equipe1->getNiveauTotalPersos();
        ModJoueurVueJoueur::afficherEquipe($this->_equipe1);
        echo '<br><br>';
        echo '_________________________________________VS_________________________________________';
        echo '<br><br>';
        echo 'Niveau total: ' . $this->_equipe2->getNiveauTotalPersos();
        ModJoueurVueJoueur::afficherEquipe($this->_equipe2);
    }

    function combattre()
    {
        //TODO


        do {
            echo "<br>\n Tour " . $this->tour . " :";
            $this->tour();
        } while (!$this->unParticipantMort());
        $this->recompenserFinCombat();
    }

    function unParticipantMort()
    {
        return ($this->_participant_1->allPersonnagesDead || $this->_participant_2->allPersonnagesDead);
    }

    function recompenserFinCombat()
    {
        //si participant 1 gagné alors récompense:
        if (!($this->_participant_1->allPersonnageDead)) {
            // si lvl total inférieur à celui de l'enemi alors + de bonus
            if ($this->_participant_1->getNiveauTotalPersos() < $this->_participant_2->getNiveauTotalPersos()) {
                //ICI recompense sous la forme (argent, %xp)
                $recompense_p1 = array(10, 10);
            }
            else {
                $recompense_p1 = array(5, 6);
            }
            //ICI on attribue la récompense
            //TODO
            //$this->_participant_1->recompenser($recompense_p1);
        }
        //si participant 2 gagné alors récompense:
        if (!($this->_participant_2->allPersonnageDead)) {
            // si lvl total inférieur à celui de l'enemi alors + de bonus
            if ($this->_participant_2->getNiveauTotalPersos() < $this->_participant_1->getNiveauTotalPersos()) {
                //ICI recompense sous la forme (argent, %xp)
                $recompense_p2 = array(10, 10);
            }
            else {
                $recompense_p2 = array(5, 6);
            }
            //ICI on attribue la récompense
            //TODO
            //$this->_participant_2->recompenser($recompense_p2);
        }
    }

    function unTour()
    {
        $i = 0;
        $j = 0;

        while ($i < ($this->_participant_1->getNombrePersonnages()) || $j < ($this->_participant_2->getNombrePersonnages())) {

            if (!$this->_equipe1[ $i ]->isDead()) {
                //TODO attaquer
            }
            if (!$this->_equipe2[ $j ]->isDead()) {
                //TODO attaquer
            }
            $i++;
            $j++;
        }
        $this->tour++;
    }
}