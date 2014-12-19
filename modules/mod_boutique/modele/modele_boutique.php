<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

require_once MOD_BPATH . DIR_SEP . "../objects/modele_participant.php";

class Boutique extends DBMapper
{
    protected $_joueur;

    function __construct()
    {
        $this->_joueur = new joueur();
    }

    public function acheterPerso()
    {
        try {
            $this->_joueur->depenser(5);
            try {
                $this->_joueur->addPersonnage(Personnage::createPersonnageForBD(1));
            } catch (Exception $e) {
                echo $e;
                //on recredite l'argent car il y a eu une erreur
                $this->_joueur->ajouterArgent(5);
            }
        } catch (Exception $e) {
            echo $e;
        }
    }
}


