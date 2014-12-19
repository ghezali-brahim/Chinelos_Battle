<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");


abstract class  Participant extends DBMapper
{

    protected $_personnages;

    abstract function addPersonnage($personnage);
    abstract function getParticipant();
    abstract function refresh();
    /**
     * @return mixed
     */

    function _toString(){
        $listeIdPersonnages = array();
        for ($i = 0; $i < count($this->_personnages); $i++) {
            array_push($listeIdPersonnages, $this->_personnages[ $i ]->getIdPersonnage());
        }
        $listeIdPersonnagesStrings = implode("; ", $listeIdPersonnages);

        return " | ID Personnages :" . $listeIdPersonnagesStrings;
    }
    public function getPersonnages()
    {
        return $this->_personnages;
    }


}

?>
