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
	//FAIT LE
	function transferer($personnage){
	if($personnage==null){
		throw new Exception('personnage inconnu');
	}
		if( $personnage->getIdEquipes()==$this->_equipes[0]->getIdEquipes() ){
			$this->_equipes[0]->removePersonnage($personnage);
			$this->_equipes[1]->addPersonnage($personnage);
		}else if( $personnage->getIdEquipes() == $this->_equipes[1]->getIdEquipes() ){
			$this->_equipes[1]->removePersonnage($personnage);
			$this->_equipes[0]->addPersonnage($personnage);
		}else{
			throw new Exception('transfert impossible');
		}
	
	}
	//RAjouter le 
	public function getPersonnageWithID($id_personnage){
		
		$personnage = $this->_equipes[0]->getPersonnage($id_personnage);
		if($personnage==null){
			$personnage = $this->_equipes[1]->getPersonnage($id_personnage);
		}

		return $personnage;

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

