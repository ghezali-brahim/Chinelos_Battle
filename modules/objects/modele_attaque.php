<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

class Attaque extends DBMapper
{

    protected $_id_attaque;
    protected $_nom;
    protected $_degats; // ( en pourcentage )
    protected $_mp_used;

    //protected $_element; // A voir


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
            throw new Exception("L'identifiant element :" . $id_attaque . " est une attaque inconnu.");
        }

        $this->_id_attaque = $attaqueElements['id_attaque'];
        $this->_nom        = $attaqueElements['nom'];
        $this->_degats     = $attaqueElements['degats'];
        $this->_mp_used    = $attaqueElements['mp_used'];
    }

    /**
     * Cette fonction rajoute une attaque dans la Base de donnée
     * avec les valeurs rentré en paramètre
     *
     * Si aucun paramètre en entrée, valeur par défault:
     * array("attaqueX",10,3)
     *
     * @param array ($nom, $degats, $mp_used)
     *
     * @return Attaque : Attaque qui a été créer
     */
    static function createAttaque(
        $attaqueElements = array(
            'nom'     => "attaqueX",
            'degats'  => 10,
            'mp_used' => 3))// Si aucun parametre
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
        $requete = "INSERT INTO attaque VALUES(:id_attaque, :nom, :degats, :mp_used)";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute($attaqueElements);
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }

        // On créer l'objet attaque
        return new Attaque($attaqueElements['id_attaque']);
    }

    /** Retourne l'identifiant de l'attaque
     * @return integer : Identifiant attaque
     */
    function getIdAttaque()
    {
        return $this->_id_attaque;
    }

    /** Retourne le cout en mp de l'attaque
     * @return integer : MP utilisé
     */
    function getMpUsed()
    {
        return $this->_mp_used;
    }

    /** Retourne les degats sous la forme degats/100
     * @return integer : coefficient de degats
     */
    function getDegats()
    {
        return ($this->_degats / 100);
    }

    /** Retourne le nom de l'attaque
     * @return string : nom de l'attaque
     */
    public function getNom()
    {
        return $this->_nom;
    }

    /**  Affichage de l'attaque en string
     * Sous la forme :
     *  "NOM: ID=5; Degats : 10% ; cout : 3MP"
     *
     * @return string
     */
    function __toString()
    {
        return $this->_nom . ': ID=' . $this->_id_attaque . '; Degats : ' . $this->_degats . '% ; cout : ' . $this->_mp_used . 'MP';
    }

    function afficherAttaque()
    {
        echo '<div class="attaque">';
        echo $this->_nom . ' | ';
        echo 'Degats : ' . $this->_degats . '% | ';
        echo $this->_mp_used . 'PM';
        echo '</div>';
    }


}