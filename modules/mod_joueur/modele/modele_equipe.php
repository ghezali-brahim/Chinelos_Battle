<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");


class  Equipe extends DBMapper
{
    protected $_id_equipe;// If null ==> Equipe IA
    protected $_personnages;// characters

    protected function __construct($personnages, $id_equipe)
    {
        $this->_id_equipe   = $id_equipe;
        $this->_personnages = $personnages;
    }

    static function createEquipe($niveauTotal, $id_equipe)
    {
        $personnages = array();
        //Attribution des personnages pour le robot
        $sommeNiveau = 0;
        while ($niveauTotal > $sommeNiveau) {
            //Le niveau aleatoire est généré entre 1/8 du niveau du groupe et 1/4
            $niveauAleatoire = rand($niveauTotal / 8, $niveauTotal / 2);
            if ($niveauAleatoire == 0) {
                $niveauAleatoire = 1;
            }
            $pers = Personnage::createPersonnage($niveauAleatoire);
            $sommeNiveau += $niveauAleatoire;
            array_push($personnages, $pers);
        }

        return new Equipe($personnages, $id_equipe);
    }

    static function createEquipeFromBD($id_equipe)
    {
        // Recuperation des personnages de l'équipe
        $requete = "SELECT id_personnage FROM personnage WHERE id_equipe = :id_equipe";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute(
                array(
                    'id_equipe' => $id_equipe
                ));
        } catch (PDOException $e) {
            echo 'Echec lors de la connexion : ' . $e->getMessage();
        }
        $listeIDPersonnages    = $reponse->fetchall();
        $personnagesFromEquipe = array();
        foreach ($listeIDPersonnages as $id_perso) {
            array_push($personnagesFromEquipe, Personnage::getPersonnageFromBD($id_perso['id_personnage']));
        }

        return new Equipe($personnagesFromEquipe, $id_equipe);
    }

    function addPersonnage($personnage)
    {
        if (!in_array($personnage, $this->_personnages)) {
            array_push($this->_personnages, $personnage);
            $personnage->setIDEquipe($this->_id_equipe);
        }
        else {
            throw new Exception('Erreur: le personnage que vous souhaité ajouté appartient déja à l\'equipe :' . $this->_id_equipe);
        }

    }

    function removePersonnage($personnage)
    {
        if (!array_search($personnage, $this->_personnages)) {
            throw new Exception('le personnage que vous souhaité retirer de l\'equipe n\'est pas dans cette equipe');
        }
        else {
            $key = array_search($personnage, $this->_personnages);
            array_splice($this->_personnages, $key, 1);
            $personnage->setIDEquipe();
        }

    }

    function getPersonnages()
    {
        return $this->_personnages;
    }

    function getPersonnage($id_personnage)
    {
        $personnagetoReturn = NULL;
        foreach ($this->_personnages as $personnage) {
            if ($personnage->getIdPersonnage() == $id_personnage) {
                $personnagetoReturn = $personnage;
            }
        }

        return $personnagetoReturn;
    }

    /** Retourne vrai si tous les personnages sont mort
     * @return bool
     */
    public function allPersonnagesDead()
    {
        $i = 0;
        do {
            $allDead = $this->_personnages[ $i ]->isDead();
            $i++;
        } while ($allDead);

        return $allDead;
    }

    public function getNombrePersonnages()
    {
        return count($this->_personnages);
    }

    /**
     * @return mixed
     */
    function __toString()
    {
        $listeIdPersonnages = array();
        for ($i = 0; $i < count($this->_personnages); $i++) {
            array_push($listeIdPersonnages, $this->_personnages[ $i ]->getIdPersonnage());
        }
        $listeIdPersonnagesStrings = implode("; ", $listeIdPersonnages);

        return "ID Equipe: " . $this->_id_equipe . " | Niveau total :" . $this->getNiveauTotalPersos() . " | ID Personnages :" . $listeIdPersonnagesStrings;
    }

    /** retourne le niveau total des persos d'un participants
     * @return int
     */
    public function getNiveauTotalPersos()
    {
        $somme = 0;
        foreach ($this->_personnages as $personnage) {
            $somme = $somme + ($personnage->getNiveau());
        }

        return $somme;
    }
}
