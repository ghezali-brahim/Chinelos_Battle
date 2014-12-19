<?php
/**
 * Created by PhpStorm.
 * User: Caporal
 * Date: 13/11/2014
 * Time: 13:30
 */
//TODO TYPE
class Item_type extends DBMapper
{
    protected $_id_type; // type d'item, exemple potions
    protected $_nom;
    protected $_actions;// listes des effets de l'item

    function __construct($id_type)
    {
        //ICI on récupère les informations de l'attaque
        $requete = "SELECT DISTINCT * FROM item_type WHERE id_type = :id_type";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute(
                array(
                    'id_type' => $id_type
                ));
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }
        $typeElements = $reponse->fetch();
        if ($typeElements == NULL) {
            throw new Exception("L'identifiant element :" . $typeElements . " est une attaque inconnu.");
        }

        $this->_id_type = $typeElements['id_attaque'];
        $this->_nom     = $typeElements['nom'];
        $this->_actions = $typeElements['actions'];
    }
}
