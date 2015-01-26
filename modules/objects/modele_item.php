<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


//TODO Item
class Item extends DBMapper {

    protected $_id_item;
    protected $_nom;
    protected $_description;
    protected $_prix_achat;// prix d'achat; prix de vente = achat/10
    protected $_type; // type d'item, exemple potions
    protected $_actions;// listes des effets de l'item (valeurs)

    function __construct ( $id_item ) {
        //ICI on récupère les informations de l'item
        $requete = "SELECT DISTINCT * FROM item WHERE id_item = :id_item";
        try {
            $reponse = self::$database->prepare ( $requete );
            $reponse->execute ( array ( 'id_item' => $id_item ) );
        } catch ( PDOException $e ) {
            echo 'Échec lors de la connexion : ' . $e->getMessage ();
        }
        $itemElements = $reponse->fetch ();
        if ( $itemElements == NULL ) {
            throw new Exception( "L'identifiant element :" . $id_item . " est un item inconnu." );
        }
        $this->_id_item     = $itemElements[ 'id_item' ];
        $this->_nom         = $itemElements[ 'nom' ];
        $this->_description = $itemElements[ 'description' ];
        $this->_type        = $itemElements[ 'type' ];
        $this->_prix_achat  = $itemElements[ 'prix_achat' ];
        $this->_actions     = $itemElements[ 'id_action' ];
    }

    /**
     * @param $id_item
     * @param $nouveauPrix
     */
    function setPrixItem ( $id_item, $nouveauPrix ) {
        $requete = "UPDATE item set VALUES prix_achat=$nouveauPrix";
        try {
            $reponse = self::$database->prepare ( $requete );
            $reponse->execute ();
        } catch ( PDOException $e ) {
            echo 'Échec lors de la connexion : ' . $e->getMessage ();
        }
        $this->_id_item = $id_item;
    }


    /**
     * @param $id_Personnage
     */
    function faireAction ($id_Personnage) {
         $this->_actions->make_action($id_Personnage);
    }
    function getArray(){
        return array('id_item'=> $this->_id_item, 'nom' => $this->_nom);
    }
}
