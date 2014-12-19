<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

//require_once MOD_BPATH.DIR_SEP."modele/modele_participant.php";

class Joueur_IA extends Participant
{
    protected $_id_robot;
    static    $indice_robot;
    static    $id_last_personnage;

    function __construct()
    {
        if (isset($indice_robot)) {
            $indice_robot = 0;
        }
        global $indice_robot;
        global $id_last_personnage;
        $indice_robot++;

        $this->_id_robot = $indice_robot;

        // Recuperation des personnages sans id_user
        $requete = "SELECT * FROM personnage WHERE id_user is null";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute();
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }
        $listPersonnages                = $reponse->fetchall();
        $this->_personnages = array();
        // Nombre de perso générer aléatoirement entre 1 à 4
        $nombre_perso       = rand(1, 4);
        if ($id_last_personnage == NULL) {
            $id_last_personnage = 0;
        }
        $i = $id_last_personnage;

        //Attribution des personnages pour le robot
        while ($i < ($nombre_perso + $id_last_personnage) && $i < count($listPersonnages)) {
            $pers = new Personnage($listPersonnages[ $i ]);
            array_push($this->_personnages, $pers);
            $i++;
        }
        $id_last_personnage = $i;
    }

    /** Ajoute un personnage au robot courant
     * Verifie si le personnage n'appartient pas déja au joueur
     * Modifie l'id_user du personnage pour mettre celui du joueur courant
     * @param $personnage
     */
    function addPersonnage($personnage)
    {
        if (!in_array($personnage, $this->_personnages)) {
            $personnage->setIdUser($this->_id_user);
            array_push($this->_personnages, $personnage);
        }
        else {
            echo 'Erreur: le personnage que vous souhaité ajouté appartient déja au joueur' . $this->_username;
        }

    }

    function refresh()
    {
        // TODO: Implement refresh() method.
    }

    /** Appel la fonction mère pour l'affichage de la liste des personnages
     * @return mixed|string
     */
    function _toString()
    {
         return "Identifiant robot: " . $this->_id_robot . parent::_toString();
    }

    public function getParticipant()
    {
        return array(
            'id_robot;'     => $this->_id_robot,
            'personnages' => $this->_personnages
        );
    }

}

?>
