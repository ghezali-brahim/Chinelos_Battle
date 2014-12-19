<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

//require_once MOD_BPATH.DIR_SEP."modele/modele_participant.php";

class Attaque extends DBMapper
{

    protected $_id_attaque;
    protected $_nom;
    protected $_degats; // ( en pourcentage )
    protected $_pm_used;


    function __construct($id_attaque)
    {
        //ICI on récupère les informations de l'attaque
        $requete = "SELECT DISTINCT * FROM attaque WHERE id_attaque = :id_attaque";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute(
                array(
                    'id_attaque' => $id_attaque
                ));
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }
        $attaqueElements = $reponse->fetch();
        if ($attaqueElements == NULL) {
            throw new Exception('l\'id "' . $id_attaque . '" attaque incorrecte.');
        }

        $this->_id_attaque = $attaqueElements['id_attaque'];
        $this->_nom        = $attaqueElements['nom'];
        $this->_degats     = $attaqueElements['degats'];
        $this->_pm_used    = $attaqueElements['pm_used'];
    }

    static function createAttaque(
        $attaqueElements = array(
            'nom'     => "attaqueX",
            'degats'  => 10,
            'pm_used' => 3))
    {
        //ICI on récupère l'id attaque max
        $requete = "SELECT max(id_attaque) FROM attaque";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute();
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }
        // Ici on détermine l'identifiant attaque max +1 afin d'en créer un nouveau
        $resultat   = $reponse->fetch();
        $id_attaque = array(
            'id_attaque' => $resultat[0] + 1);

        // On prépare les éléments pour la création du Personnage
        $attaqueElements = array_merge($id_attaque, $attaqueElements);

        //ICI on créer l'attaque dans la base de donnée
        $requete = "INSERT INTO attaque VALUES(:id_attaque, :nom, :degats, :pm_used)";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute($attaqueElements);
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }

        // On créer l'objet attaque
        return new Attaque($attaqueElements['id_attaque']);
    }

    /**
     * @return mixed
     */
    public function getIdAttaque()
    {
        return $this->_id_attaque;
    }

    /**
     * @return mixed
     */
    public function getPmUsed()
    {
        return $this->_pm_used;
    }

    /**
     * @return mixed
     */
    public function getDegats()
    {
        return ($this->_degats / 100);
    }

    function __toString()
    {
        return $this->_nom . ': ID=' . $this->_id_attaque . '; Degats : ' . $this->_degats . '% ; cout : ' . $this->_pm_used . 'PM';
    }


}