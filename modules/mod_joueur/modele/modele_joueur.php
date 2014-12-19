<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

//require_once MOD_BPATH.DIR_SEP."modele/modele_participant.php";

class Joueur extends Participant
{
    //Player

    protected $_id_user;
    protected $_username;
    protected $_argent;

    /**
     * On créer un Joueur à partir de la base de données; On verifie d'abbord si le joueur est connecté.
     */
    function __construct()
    {
        //Verification de la connexion
        if (!isset($_SESSION['username']) && !isset($_SESSION['id_user'])) {
            die ("Vous n'etes pas connectée");
        }
        else {
            if ($_SESSION['username'] == NULL || $_SESSION['id_user'] == NULL) {
                die ("Vous n'etes pas connectée");
            }
            $requete = "SELECT id_user, username, argent FROM users WHERE username = :username AND id_user = :id_user";
            try {
                $reponse = self::$database->prepare($requete);
                $reponse->execute(
                    array(
                        'username' => $_SESSION ['username'],
                        'id_user'  => $_SESSION ['id_user']
                    ));
            } catch (PDOException $e) {
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }

            // Verifie la validité des valeurs de sessions
            // Si les valeurs de sessions n'existe pas dans la bdd; alors erreur
            if ($reponse->rowCount() == 0) {
                die ("Vous n'etes pas connectée, vos identifiants de sessions sont des faux");
            }
            // FIN verification connexion

            //Recuperation infos du joueur
            $resultat        = $reponse->fetch();
            $this->_id_user  = $resultat['id_user'];
            $this->_username = $resultat['username'];
            $this->_argent   = $resultat['argent'];

            // Recuperation de la liste des équipes
            $requete = "SELECT id_equipe FROM equipe WHERE id_user = :id_user";
            try {
                $reponse = self::$database->prepare($requete);
                $reponse->execute(
                    array(
                        'id_user' => $_SESSION ['id_user']
                    ));
            } catch (PDOException $e) {
                echo 'Echec lors de la connexion : ' . $e->getMessage();
            }
            $listeEquipes   = $reponse->fetchall();
            $this->_equipes = array();

            foreach ($listeEquipes as $id_equipe) {
                $equipe = Equipe::createEquipeFromBD($id_equipe['id_equipe']);
                array_push($this->_equipes, $equipe);
            }

        }


    }

    /** Ajoute un personnage au joueur courant
     * Verifie si le personnage n'appartient pas déja au joueur
     * Modifie l'id_user du personnage pour mettre celui du joueur courant
     *
     * @param $personnage
     */
    //TODO IMPORTANT IL FAUT FAIRE EN SORTE D'ENLEVER LE PERSONNAGE DEPUIS SON ANCIEN PARTICIPANT
    function addPersonnage($personnage)
    {
        $this->_equipes[0]->addPersonnage($personnage);
    }

    /** Rafraichit le joueur depuis la bdd
     *
     */
    function refresh()
    {
        // TODO: Implement refresh() method.
    }

    /** Appel la fonction mère pour l'affichage de la liste des personnages
     * @return mixed|string
     */
    function __toString()
    {
        return "Identifiant joueur: " . $this->_id_user . parent::__toString();
    }


    public function getParticipant()
    {
        return array(
            'id_user'  => $this->_id_user,
            'username' => $this->_username,
            'argent'   => $this->_argent,
            'equipes'  => $this->_equipes
        );
    }
	//Fait le 
	public function getNiveauTotalJoueur(){
		return $this->_equipes[0]->getNiveauTotalPersos();
	}
}


