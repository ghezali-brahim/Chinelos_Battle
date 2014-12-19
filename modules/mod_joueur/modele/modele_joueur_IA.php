<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

//require_once MOD_BPATH.DIR_SEP."modele/modele_participant.php";

class Joueur_IA extends Participant
{
    static    $indice_robot;
    protected $_id_robot;

    function __construct($niveauTotal)
    {
        if (isset($indice_robot)) {
            $indice_robot = 0;
        }
        global $indice_robot;
        $indice_robot++;

        $this->_id_robot = $indice_robot;

        $this->_equipes = array(Equipe::createEquipe($niveauTotal, $this->_id_robot));

    }


    function refresh()
    {
        // TODO: Implement refresh() method.
    }

    /** Appel la fonction mère pour l'affichage de la liste des personnages
     * @return mixed|string
     */
    function __toString()
    {
        return "Identifiant robot: " . $this->_id_robot . parent::__toString();
    }

    public function getParticipant()
    {
        return array(
            'id_robot;' => $this->_id_robot,
            'equipes'   => $this->_equipes
        );
    }

    //TODO
    function addPersonnage($personnage)
    {
        //$this->_equipes[0]->addPersonnage($personnage);
    }
}

