<?php

/**
 * Created by PhpStorm.
 * User: Caporal
 * Date: 09/11/2014
 * Time: 22:16
 */
//TODO Faire un mode debug avec des logs
class Element extends DBMapper
{

    private static $dossierIcone = "img/element/";

    /**
     * Retourne le lien vers l'image correspondant à l'élement
     * en chaine de caractère
     *
     * @param $id_element
     *
     * @return string lien vers l'image de l'élement
     */
    static public function getIcone ( $id_element )
    {
        return self::$dossierIcone . $id_element . ".png";
    }

    /**
     * Retourne l'ensemble du contenu de l'élement dont l'id_element a été renseigné
     *
     * @param $id_element
     *
     * @return contenu ligne element de la BD
     * @throws Exception
     */
    static public function getElement ( $id_element )
    {
        return Element::selectFromBD ( $id_element, "SELECT * FROM element WHERE id_element = :id_element" )[ 0 ];
    }

    /**
     * Envoie une requete vers la base de donnée et retourne le résultat
     *
     * @param        $id_element
     * @param string $requete
     *
     * @return mixed
     * @throws Exception
     */
    private static function selectFromBD ( $id_element, $requete = "SELECT DISTINCT * FROM element WHERE id_element = :id_element" )
    {
        //ICI on récupère les informations de l'attaque
        try {
            $reponse = self::$database->prepare ( $requete );
            $reponse->execute (
                    array (
                            'id_element' => $id_element
                    ) );
        } catch ( PDOException $e ) {
            echo 'Échec lors de la connexion : ' . $e->getMessage ();
        }
        $contenuElement = $reponse->fetchAll ();
        if ( $contenuElement == NULL ) {
            throw new Exception( "L'identifiant element :" . $id_element . " est un element inconnu." );
        }
        //TODO je suis persuadé qu'il y a un probleme => go voir les logs
        if ( static::$db_debug ) {
            $i     = 0;
            $infos = "";
            foreach ( $contenuElement[ 0 ] as $info ) {
                if ( $i == 0 ) {
                    $infos = $info;
                } else {
                    $infos .= "|" . $info;
                }
                $i++;
            }
            static::log ( $requete . " : \t $infos", "requete_" . get_called_class () );
        }

        return $contenuElement;
    }

    /**
     * Retourne le nom de l'élement dont l'id_element a été renseigné
     *
     * @param $id_element
     *
     * @return String Nom de l'element
     * @throws Exception
     */
    static public function getNom ( $id_element )
    {
        return Element::selectFromBD ( $id_element, "SELECT nom FROM element WHERE id_element = :id_element" )[ 0 ][ 'nom' ];
    }

    /**
     * Retourne un ratio de degat d'un élément sur un autre en fonction de l'id_element
     * SI Fort contre alors :   1.5
     * SI Faible contre alors : 0.5
     * Sinon                  : 1.0
     *
     * @param $id_element
     * @param $id_element_target
     *
     * @return float|int
     */
    static function getRatioDegatElement ( $id_element, $id_element_target )
    {
        if ( in_array ( $id_element_target, Element::getIdElementFortContre ( $id_element ) ) ) {
            $ratio = 1.5;
        } else if ( in_array ( $id_element_target, Element::getIdElementFaibleContre ( $id_element ) ) ) {
            $ratio = 0.5;
        } else {
            $ratio = 1;
        }

        return $ratio;
    }

    /**
     * Retourne sous forme de liste d'entier la liste des id_element contre lequels l'id_element spécifié est fort
     *
     * @param $id_element
     *
     * @return array(Integer) ; liste des id elements
     * @throws Exception
     */
    static function getIdElementFortContre ( $id_element )
    {
        $id_fort_contre_String = Element::selectFromBD ( $id_element, "SELECT id_fort_contre FROM element WHERE id_element = :id_element" )[ 0 ][ 'id_fort_contre' ];

        return explode ( ";", $id_fort_contre_String );
    }

    /**
     * Retourne sous forme de liste d'entier la liste des id_element contre lequels l'id_element spécifié est faible
     *
     * @param $id_element
     *
     * @return array(Integer) ; liste des id elements
     * @throws Exception
     */
    static function getIdElementFaibleContre ( $id_element )
    {
        $id_faible_contre_String = Element::selectFromBD ( $id_element, "SELECT id_faible_contre FROM element WHERE id_element = :id_element" )[ 0 ][ 'id_faible_contre' ];

        return explode ( ";", $id_faible_contre_String );
    }
}