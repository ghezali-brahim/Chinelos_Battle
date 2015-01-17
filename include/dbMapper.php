<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once ( "Logger.class.php" );


class DBMapper
{
    protected static $database;
    protected static $db_debug;
    protected static $db_log;

    static function init ( $db )
    {
        self::$database = $db;
        self::$db_debug = TRUE;
        self::$db_log;
    }

    /**     Cette méthode permet d'envoyer des requetes sur la BD
     * et de récupérer le resultat de la requete
     *  Si vous souhaité une seul ligne, il faut récupéré l'indice 0
     * sur la valeur de retour,
     * exemple : return $resultat;
     *          $resultat[0]
     *
     * @param       $requete
     * @param array $donnees
     *
     * @return array de resultat dans le cas d'un select ou String
     * @throws Exception
     */
    protected static function requeteFromDB ( $requete, $donnees = array () )
    {
        if ( $requete == NULL ) {
            throw new Exception( "Impossible de faire la requete car requete ou donnees vide" );
        }
        //Execution de la requete
        try {
            $reponse = self::$database->prepare ( $requete );
            if ( $reponse->execute ( $donnees ) == FALSE ) {
                throw new Exception ( "Requete a échoué, code erreur :" . $reponse->errorCode () . ";" );
            }
        } catch ( PDOException $e ) {
            echo 'Échec lors de la connexion : ' . $e->getMessage ();
        }
        $resultat = $reponse->fetchAll ();
        if ( self::$db_debug ) {
            $i     = 0;
            $infos = "";
            foreach ( $donnees as $info ) {
                if ( $i == 0 ) {
                    $infos = $info;
                } else {
                    $infos .= "|" . $info;
                }
                $i++;
            }
            static::log ( $requete . " : \t $infos", "requete_" . get_called_class () );
        }

        return $resultat;
    }

    protected static function log ( $message, $nomfichier = NULL )
    {
        if ( $nomfichier == NULL ) {
            $nomfichier = get_called_class ();
        }
        $nomfichier = strtolower ( $nomfichier );
        if ( !isset( self::$db_log ) ) {
            self::$db_log = new Logger( "./logs" );
            self::$db_log->log ( 'erreurs', "" . $nomfichier, "___________________________________________", Logger::GRAN_MONTH );
        }
        self::$db_log->log ( 'erreurs', "" . $nomfichier, $message, Logger::GRAN_MONTH );
        $requete = strpbrk ( $nomfichier, '_' );
        if ( $requete ) {
            $nomfichier = substr ( $requete, 1 );
        }
        self::$db_log->log ( 'erreurs', 'statistique_' . $nomfichier, $message, Logger::GRAN_MONTH );
    }
    //parent::requeteFromDB


}
