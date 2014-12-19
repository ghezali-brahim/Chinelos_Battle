<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

//require_once MOD_BPATH.DIR_SEP."modele/modele_participant.php";

class Attaque extends DBMapper
{

    protected $id_attaque;
    protected $nom;
    protected $degats; // ( en pourcentage )
    protected $pm_used;


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

        $this->id_attaque = $attaqueElements['id_attaque'];
        $this->nom        = $attaqueElements['nom'];
        $this->degats     = $attaqueElements['degats'];
        $this->pm_used    = $attaqueElements['pm_used'];
    }

}