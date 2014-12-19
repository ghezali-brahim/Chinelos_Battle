<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");


abstract class  Participant extends DBMapper
{
    protected $_equipes;

    abstract function addPersonnage($personnage);

    abstract function getParticipant();

    abstract function refresh();

    function getEquipeOne()
    {
        return $this->_equipes[0];
    }

    function __toString()
    {
        return "; " . $this->_equipes[0]->__toString();
    }

    public function getEquipes()
    {
        return $this->_equipes;
    }
}

