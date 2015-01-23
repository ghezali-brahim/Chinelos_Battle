<?php
if ( ! defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


//TODO Transformer la structure d'attaque et la mettre comme element == evite les requetes inutiles car attaque communs à tout le monde
class Attaque extends DBMapper {

    protected $_id_attaque;
    protected $_nom;
    protected $_degats; // ( en pourcentage )
    protected $_mp_used;
    //protected $_element; // A voir
    /**
     * Constructeur
     *
     * @param $id_attaque
     *
     * @throws Exception
     */
    function __construct ( $id_attaque ) {
        //ICI on récupère les informations de l'attaque
        $resultat        = static::requeteFromDB ( "SELECT * FROM attaque WHERE id_attaque = :id_attaque", array ( 'id_attaque' => $id_attaque ) );
        $attaqueElements = $resultat[ 0 ];
        if ( $attaqueElements == NULL ) {
            throw new Exception( "L'identifiant element :" . $id_attaque . " est une attaque inconnu." );
        }
        $this->_id_attaque = $attaqueElements[ 'id_attaque' ];
        $this->_nom        = $attaqueElements[ 'nom' ];
        $this->_degats     = $attaqueElements[ 'degats' ];
        $this->_mp_used    = $attaqueElements[ 'mp_used' ];
        if ( self::$db_debug ) {
            static::log ( "Construction " . __CLASS__ . " : " . $this->__toString () );
        }
    }

    /**
     * Affichage de l'attaque en string
     * Sous la forme :
     *  "NOM: ID=5; Degats : 10% ; cout : 3MP"
     *
     * @return string
     */
    function __toString () {
        return $this->_nom . ': ID=' . $this->_id_attaque . '; Degats : ' . $this->_degats . '% ; cout : ' . $this->_mp_used . 'MP';
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
    static function createAttaque ( $attaqueElements = array ( 'nom' => "attaqueX", 'degats' => 10, 'mp_used' => 3 ) )// Si aucun parametre
    {
        if ( self::$db_debug ) {
            static::log ( "Creation d'une attaque de nom : " . $attaqueElements[ 'nom' ] );
        }
        //ICI on créer l'attaque dans la base de donnée
        static::requeteFromDB ( "INSERT INTO attaque VALUES(\"\", :nom, :degats, :mp_used)", $attaqueElements );
        //TODO trouver une meilleur maniere de trouver l'id attaque
        $id_attaque = static::requeteFromDB ( "SELECT id_attaque FROM attaque ORDER BY DESC" )[ 0 ];

        // On créer l'objet attaque
        return new Attaque( $id_attaque );
    }

    /**
     * Retourne l'identifiant de l'attaque
     * @return integer : Identifiant attaque
     */
    function getIdAttaque () {
        return $this->_id_attaque;
    }

    /**
     * Retourne le cout en mp de l'attaque
     * @return integer : MP utilisé
     */
    function getMpUsed () {
        return $this->_mp_used;
    }

    /**
     * Retourne les degats sous la forme degats/100
     * @return integer : coefficient de degats
     */
    function getDegats () {
        return ( $this->_degats / 100 );
    }

    /**
     * Retourne le nom de l'attaque
     * @return string : nom de l'attaque
     */
    public function getNom () {
        return $this->_nom;
    }

    function afficherAttaque () {
        echo '<div class="attaque">';
        echo $this->_nom . ' | ';
        echo 'Degats : ' . $this->_degats . '% | ';
        echo $this->_mp_used . 'PM';
        echo '</div>';
    }

    /**
     * Retourne l'affichage des attaques
     * @return string
     */
    function retourneAffichageAttaque () {
        $text = "";
        $text .= '<div class="attaque">';
        $text .= $this->_nom . ' | ';
        $text .= 'Degats : ' . $this->_degats . '% | ';
        $text .= $this->_mp_used . 'PM';
        $text .= '</div>';

        return $text;
    }
}