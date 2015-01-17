<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


class Niveau extends DBMapper
{

    /**
     * etourne le pourcentage d'experience réalisé pour monter au niveau suivant
     *
     * @param $experience
     *
     * @return float
     */
    static public function getPourcentXp ( $experience )
    {
        $niveau               = Niveau::getNiveau ( $experience );
        $niveauSuivant        = $niveau + 1;
        $experienceNecessaire = Niveau::getXpNiveau ( $niveauSuivant ) - Niveau::getXpNiveau ( $niveau );
        $pourcentageEffectue  = ( 1 - ( Niveau::getXpManquantPourUp ( $experience ) / $experienceNecessaire ) ) * 100;

        return round ( $pourcentageEffectue, 2 );
    }

    /**
     * Retourne le niveau correspondant à l'experience actuelle
     *
     * @param $experience
     *
     * @return mixed
     */
    static public function getNiveau ( $experience )
    {
        $niveauActuel = static::requeteFromDB ( "SELECT DISTINCT niveau FROM niveau WHERE experience <= :experience ORDER BY niveau DESC limit 1", array (
                'experience' => ( $experience )
        ) )[ 0 ];

        return $niveauActuel[ 'niveau' ];
    }

    /**
     * Retourne l'experience necessaire pour le niveau spécifié
     *
     * @param $niveau
     *
     * @return mixed
     */
    static public function getXpNiveau ( $niveau )
    {
        $niveauActuel = static::requeteFromDB ( "SELECT DISTINCT experience FROM niveau WHERE niveau = :niveau", array (
                'niveau' => ( $niveau )
        ) )[ 0 ];

        return $niveauActuel[ 'experience' ];
    }

    /**
     * Retourne l'experience manquant pour monter le niveau suivant
     *
     * @param $experience
     *
     * @return int
     */
    static public function getXpManquantPourUp ( $experience )
    {
        $niveau               = Niveau::getNiveau ( $experience );
        $experienceNecessaire = Niveau::getXpNiveau ( $niveau + 1 );

        return ( $experienceNecessaire - $experience );
    }
}

