<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

class Niveau extends DBMapper
{

    /** Retourne le pourcentage d'experience réalisé pour monter au niveau suivant
     *
     * @param $experience
     *
     * @return float
     */
    static public function getPourcentXp($experience)
    {
        $niveau               = Niveau::getNiveau($experience);
        $niveauSuivant        = $niveau + 1;
        $experienceNecessaire = Niveau::getXpNiveau($niveauSuivant) - Niveau::getXpNiveau($niveau);
        $pourcentageEffectue  = (1 - (Niveau::getXpManquantPourUp($experience) / $experienceNecessaire)) * 100;

        return round($pourcentageEffectue, 2);
    }

    /** Retourne le niveau correspondant à l'experience actuelle
     *
     * @param $experience
     *
     * @return mixed
     */
    static public function getNiveau($experience)
    {
        $requete = "SELECT DISTINCT niveau FROM niveau WHERE experience <= :experience ORDER BY niveau DESC limit 1";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute(
                array(
                    'experience' => ($experience)
                ));
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }
        $niveauActuel = $reponse->fetch();

        return $niveauActuel['niveau'];
    }

    /** Retourne l'experience necessaire pour le niveau spécifié
     *
     * @param $niveau
     *
     * @return mixed
     */
    static public function getXpNiveau($niveau)
    {
        $requete = "SELECT DISTINCT experience FROM niveau WHERE niveau = :niveau";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute(
                array(
                    'niveau' => ($niveau)
                ));
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }
        $niveauActuel = $reponse->fetch();

        return $niveauActuel['experience'];
    }

    /** retourne l'experience manquant pour monter le niveau suivant
     *
     * @param $experience
     *
     * @return mixed
     */
    static public function getXpManquantPourUp($experience)
    {
        $niveau               = Niveau::getNiveau($experience);
        $experienceNecessaire = Niveau::getXpNiveau($niveau + 1);

        return ($experienceNecessaire - $experience);
    }
}

