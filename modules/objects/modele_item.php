<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


//TODO Item
class Item extends DBMapper
{

    protected $_id_item;
    protected $_nom;
    protected $_description;
    protected $_prix_achat;// prix d'achat; prix de vente = achat/10
    protected $_type; // type d'item, exemple potions
    protected $_actions;// listes des effets de l'item (valeurs)

    function __construct ( $id_item )
    {
        //ICI on récupère les informations de l'item
        $requete = "SELECT DISTINCT * FROM item WHERE id_item = :id_item";
        try {
            $reponse = self::$database->prepare ( $requete );
            $reponse->execute (
                    array (
                            'id_item' => $id_item
                    ) );
        } catch ( PDOException $e ) {
            echo 'Échec lors de la connexion : ' . $e->getMessage ();
        }
        $itemElements = $reponse->fetch ();
        if ( $itemElements == NULL ) {
            throw new Exception( "L'identifiant element :" . $id_item . " est une attaque inconnu." );
        }
        $this->_id_item     = $itemElements[ 'id_item' ];
        $this->_nom         = $itemElements[ 'nom' ];
        $this->_description = $itemElements[ 'description' ];
        $this->_type        = $itemElements[ 'type' ];
        $this->_prix_achat  = $itemElements[ 'prix_achat' ];
        $this->_actions     = $itemElements[ 'actions' ];
    }
}
