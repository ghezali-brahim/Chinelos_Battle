<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");
require_once MOD_BPATH . DIR_SEP . "../objects/modele_personnage.php";

class  Equipe extends DBMapper
{
    protected $_id_equipe;// If null ==> Equipe IA
    protected $_personnages;// characters

    protected function __construct($personnages, $id_equipe)
    {
        $this->_id_equipe   = $id_equipe;
        $this->_personnages = $personnages;
    }

    /** Permet de créer une équipe en fonction du niveau pour les joueur_IA
     *
     * @param $niveauTotal
     * @param $id_equipe
     *
     * @return Equipe
     */
    static function createEquipe($niveauTotal, $id_equipe)
    {
        //On limite la taille de l'équipe à 6 personnage
        do {
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
        } while (count($personnages) >= 6);

        return new Equipe($personnages, $id_equipe);
    }

    /** Permet de créer une équipe en récupéré à partir de la BDD
     *
     * @param $id_equipe
     *
     * @return Equipe
     */
    static function createEquipeFromBD($id_equipe)
    {
        // Recuperation des personnages de l'équipe
        $requete = "SELECT id_personnage FROM personnage WHERE id_equipe = :id_equipe order by niveau";
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

    static function comparerNiveauDeuxPersonnage($p1, $p2)
    {
        return Personnage::comparerNiveauPersonnage($p1, $p2);
    }

    /** Ajoute un personnage à l'équipe
     *
     * @param $personnage
     *
     * @throws Exception
     */
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

    /** Retire un personnage de l'équipe
     *
     * @param $personnage
     *
     * @throws Exception
     */
    function removePersonnage($personnage)
    {
        if (!in_array($personnage, $this->_personnages)) {
            throw new Exception('le personnage que vous souhaité retirer de l\'equipe n\'est pas dans cette equipe');
        }
        else {
            $key = array_search($personnage, $this->_personnages);
            array_splice($this->_personnages, $key, 1);
            $personnage->setIDEquipe();
        }
    }

    /**
     * @return int
     */
    function getIdEquipe()
    {
        return $this->_id_equipe;
    }

    /**
     * @return array(Personnages)
     */
    function getPersonnages()
    {
        return $this->_personnages;
    }

    /**
     * @param $id_personnage
     * Si personnage n'existe pas dans l'équipe alors renvoie null
     *
     * @return Personnage
     */
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

    /**
     * @return mixed
     */
    function __toString()
    {
        /*
         * ANCIENNE VERSION AVEC SEULEMENT ID PERSO
        $listeIdPersonnages = array();
        for ($i = 0; $i < count($this->_personnages); $i++) {
            array_push($listeIdPersonnages, $this->_personnages[ $i ]->getIdPersonnage());
        }
        $listeIdPersonnagesStrings = implode("; ", $listeIdPersonnages);
        */
        $listePersonnages = array();
        foreach ($this->_personnages as $personnage) {
            array_push($listePersonnages, $personnage->__toString());
        }
        $listePersonnagesStrings = implode(" | ", $listePersonnages);

        return "ID Equipe: " . $this->_id_equipe . " | Niveau total :" . $this->getNiveauTotalPersos() . " | Personnages :" . $listePersonnagesStrings;
    }

    /** retourne le niveau total des persos d'une équipe
     * @return int
     */
    function getNiveauTotalPersos()
    {
        $somme = 0;
        foreach ($this->_personnages as $personnage) {
            $somme = $somme + ($personnage->getNiveau());
        }

        return $somme;
    }

    function retourneAffichageEquipe()
    {
        $text = '<div class="equipe">';

        foreach ($this->_personnages as $personnage) {
            $text = $text . $personnage->retourneAffichagePersonnage();
        }
        $text = $text . '</div>';

        return $text;
    }

    function afficherEquipe()
    {
        echo '<div class="equipe">';
        foreach ($this->_personnages as $personnage) {
            $personnage->afficherPersonnage();
        }
        echo '</div>';
    }

    /** Retourne le personnage le plus faible de l'equipe
     * (c'est à dire avec le moins de point de vie)
     * @return Personnage
     */
    function getPersonnagePlusFaibleVivant()
    {
        if ($this->allPersonnagesDead()) {
            throw new Exception('Aucun personnage Vivant');
        }
        $personnageAretournee = NULL;
        foreach ($this->_personnages as $personnage) {
            if (!$personnage->isDead()) {
                if (($personnageAretournee) == NULL) {
                    $personnageAretournee = $personnage;
                }
                else {
                    if ($personnage->getNiveau() < $personnageAretournee->getNiveau()) {
                        $personnageAretournee = $personnage;
                    }
                }

            }
        }

        return $personnageAretournee;
    }

    /** Retourne vrai si tous les personnages sont mort
     * @return bool
     */
    function allPersonnagesDead()
    {
        $i = 0;
        do {
            $allDead = $this->_personnages[ $i ]->isDead();
            $i++;
        } while ($allDead && $i < $this->getNombrePersonnages());

        return $allDead;
    }

    /**
     * @return int
     */
    function getNombrePersonnages()
    {
        return count($this->_personnages);
    }

    function ajouterPourcentExperience($pourcentXP)
    {

        foreach ($this->_personnages as $personnage) {
            $personnage->addPourcentExperience($pourcentXP);
        }
    }

    function trierPersonnageParNiveau()
    {
        if (usort($this->_personnages, array("Personnage", "comparerNiveauPersonnage"))) {
        }
        else {
            throw new Exception("Le trie a échoué");
        }
    }

    /** Retourne    0 si ==
     *              1 si $p1 > $p2
     *              -1 si $p1 > $p2
     *
     * @param $p1
     * @param $p2
     *
     * @return int
     */
    function comparer_niveau($p1, $p2)
    {
        if ($p1->getNiveau() == $p2->getNiveau()) {
            return 0;
        }

        return ($p1->getNiveau() < $p2->getNiveau()) ? -1 : 1;
    }
}
