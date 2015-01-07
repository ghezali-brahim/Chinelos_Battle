<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "../objects/modele_personnage.php";


class  Equipe extends DBMapper
{
    protected $_id_equipe;// If null ==> Equipe IA
    protected $_personnages;// characters
    protected $_indice_perso_actuel;

    /**
     * Constructeur
     *
     * @param $personnages
     * @param $id_equipe
     */
    protected function __construct ( $personnages, $id_equipe )
    {
        $this->_id_equipe           = $id_equipe;
        $this->_personnages         = $personnages;
        $this->_indice_perso_actuel = 0;
        if ( self::$db_debug ) {
            static::log ( "Construction " . __CLASS__ . " : " . $this->__toString () );
        }
    }

    /**
     * @return mixed
     */
    function __toString ()
    {
        $listePersonnages = array ();
        foreach ( $this->_personnages as $personnage ) {
            array_push ( $listePersonnages, $personnage->__toString () );
        }
        $listePersonnagesStrings = implode ( " | ", $listePersonnages );

        return "ID Equipe: " . $this->_id_equipe . " | Niveau total :" . $this->getNiveauTotalPersos () . " | Personnages :" . $listePersonnagesStrings;
    }

    /**
     * Retourne le niveau total des persos d'une équipe
     * @return int Niveau Total
     */
    function getNiveauTotalPersos ()
    {
        $somme = 0;
        foreach ( $this->_personnages as $personnage ) {
            $somme = $somme + ( $personnage->getNiveau () );
        }

        return $somme;
    }

    /**
     * Permet de créer une équipe en fonction du niveau pour les joueur_IA
     *
     * @param $niveauTotal
     * @param $id_equipe
     *
     * @return Equipe
     */
    static function createEquipe ( $niveauTotal, $id_equipe )
    {
        if ( self::$db_debug ) {
            static::log ( "Creation d'une l'equipe : " . "..." );
        }
        //On limite la taille de l'équipe à 6 personnage
        do {
            $personnages = array ();
            //Attribution des personnages pour le robot
            $i           = 0;
            $sommeNiveau = 0;
            while ( $niveauTotal > $sommeNiveau ) {
                //Le niveau aleatoire est généré entre 1/8 du niveau du groupe et 1/4
                $i++;
                $niveauAleatoire = rand ( $niveauTotal / 8, $niveauTotal / 2 );
                if ( $niveauAleatoire == 0 ) {
                    $niveauAleatoire = 1;
                }
                $pers = Personnage::createPersonnage ( $niveauAleatoire, "Monster " . $i );
                $sommeNiveau += $niveauAleatoire;
                array_push ( $personnages, $pers );
            }
        } while ( count ( $personnages ) >= 6 );

        return new Equipe( $personnages, $id_equipe );
    }

    /**
     * Creer deux equipe lors de la création d'un joueur
     *
     * @param $id_user
     *
     * @throws Exception
     */
    static function createTwoEquipeForBD ( $id_user )
    {
        static::log ( "Creation de deux equipe pour le joueur avec l'id : " . $id_user . " ..." );
        // Creation de l'equipe One
        static::requeteFromDB ( "INSERT INTO equipe VALUES (\"\", :id_user)", array ( 'id_user' => $id_user ) );
        $id_equipe_one = static::requeteFromDB ( "SELECT id_equipe from equipe where id_user=:id_user", array ( 'id_user' => $id_user ) )[ 0 ][ 'id_equipe' ];
        // On créer un personnage pour le joueur 1
        Personnage::createPersonnageForBD ( 1, $id_equipe_one );
        // Creation de l'equipe de remplacement
        static::requeteFromDB ( "INSERT INTO equipe VALUES (\"\", :id_user)", array ( 'id_user' => $id_user ) );
    }

    /**
     * Permet de créer une équipe en récupérant les personnages à partir de la BDD
     *
     * @param $id_equipe
     *
     * @return Equipe
     */
    static function createEquipeFromBD ( $id_equipe )
    {
        static::log ( "Recuperation des personnages de l'equipe d'id : " . $id_equipe );
        $resultat_requete   = static::requeteFromDB ( "SELECT id_personnage FROM personnage WHERE id_equipe = :id_equipe order by niveau", array (
                'id_equipe' => $id_equipe
        ) );
        $listeIDPersonnages = array ();
        // Un peu compliqué, mais on recupère ici la liste des id_personnage et on les mets en forme
        foreach ( $resultat_requete as $key => $id_perso ) {
            array_push ( $listeIDPersonnages, $id_perso[ 0 ] );
        }
        $personnagesFromEquipe = array ();
        foreach ( $listeIDPersonnages as $id_personnage ) {
            array_push ( $personnagesFromEquipe, Personnage::getPersonnageFromBD ( $id_personnage ) );
        }

        return new Equipe( $personnagesFromEquipe, $id_equipe );
    }

    /**
     * Compare le niveau de deux personnage
     * en utilisant des fonctions de comparaison dans la classe Personnage
     *
     * @param $p1 Personnage 1
     * @param $p2 Personnage 2
     *
     * @return int si positif alors $p1 > $p2
     */
    static function comparerNiveauDeuxPersonnage ( $p1, $p2 )
    {
        return Personnage::comparerNiveauPersonnage ( $p1, $p2 );
    }

    /**
     * Ajoute un personnage à l'équipe
     *
     * @param $personnage
     *
     * @throws Exception
     */
    function addPersonnage ( $personnage )
    {
        static::log ( "Ajout du personnage " . $personnage->getIdPersonnage () . " a l'equipe " . $this->_id_equipe );
        if ( !in_array ( $personnage, $this->_personnages ) ) {
            array_push ( $this->_personnages, $personnage );
            $personnage->setIDEquipe ( $this->_id_equipe );
        } else {
            throw new Exception( 'Erreur: le personnage que vous souhaité ajouté appartient déja à l\'equipe :' . $this->_id_equipe );
        }
    }

    /**
     * Retire un personnage de l'équipe
     *
     * @param $personnage
     *
     * @throws Exception
     */
    function removePersonnage ( $personnage )
    {
        static::log ( "Retrait du personnage " . $personnage->getIdPersonnage () . " de l'equipe " . $this->_id_equipe );
        if ( !in_array ( $personnage, $this->_personnages ) ) {
            throw new Exception( 'le personnage que vous souhaité retirer de l\'equipe n\'est pas dans cette equipe' );
        } else {
            $key = array_search ( $personnage, $this->_personnages );
            array_splice ( $this->_personnages, $key, 1 );
            $personnage->setIDEquipe ();
        }
    }

    /**
     * @return array(Personnages)
     */
    function getPersonnages ()
    {
        return $this->_personnages;
    }

    /**
     * @param $id_personnage
     * Si personnage n'existe pas dans l'équipe alors renvoie null
     *
     * @return Personnage
     */
    function getPersonnage ( $id_personnage )
    {
        $personnagetoReturn = NULL;
        foreach ( $this->_personnages as $personnage ) {
            if ( $personnage->getIdPersonnage () == $id_personnage ) {
                $personnagetoReturn = $personnage;
            }
        }

        return $personnagetoReturn;
    }

    /**
     * Retourne l'affichage d'une equipe et affiche les boutons d'attaque
     * au personnage correspondant à l'indice indice_perso_actuel
     * @return string
     */
    function retourneAffichageEquipeAvecAttaques ()
    {
        $text = '<div class="equipe">';
        $text .= '<h3>Indice personnage actuelle : ' . $this->_indice_perso_actuel . '</h3><br>';
        for ( $i = 0; $i < count ( $this->_personnages ); $i++ ) {
            if ( $i == $this->_indice_perso_actuel ) {
                $text = $text . $this->getPersonnageIndice ( $i )->retourneAffichagePersonnageAvecAttaque ();
            } else {
                $text = $text . $this->getPersonnageIndice ( $i )->retourneAffichagePersonnage ();
            }
        }
        $text = $text . '</div>';

        return $text;
    }

    /**
     * Retourne le personnage correspondant à l'indice dans l'equipe
     *
     * @param $indice
     *
     * @return Personnage
     * @throws Exception
     */
    function getPersonnageIndice ( $indice )
    {
        if ( !array_key_exists ( $indice, $this->_personnages ) ) {
            throw new Exception( "ID Equipe: $this->_id_equipe  ; Indice $this->_indice_perso_actuel incorrecte afin de récuperer le personnage" );
        }

        return $this->_personnages[ $indice ];
    }

    function afficherEquipe ()
    {
        /*
        echo '<div class="equipe">';
        foreach ( $this->_personnages as $personnage ) {
            $personnage->afficherPersonnage ();
        }
        echo '</div>';
        */
        echo $this->retourneAffichageEquipe ();
    }

    function retourneAffichageEquipe ()
    {
        $text = '<div class="equipe">';
        foreach ( $this->_personnages as $personnage ) {
            $text = $text . $personnage->retourneAffichagePersonnage ();
        }
        $text = $text . '</div>';

        return $text;
    }

    /**
     * Retourne le personnage le plus faible de l'equipe
     * (c'est à dire avec le moins de point de vie)
     * @return Personnage
     * @throws Exception
     */
    function getPersonnagePlusFaibleVivant ()
    {
        if ( $this->allPersonnagesDead () ) {
            throw new Exception( 'Aucun personnage Vivant' );
        }
        $personnageAretournee = NULL;
        foreach ( $this->_personnages as $personnage ) {
            if ( !$personnage->isDead () ) {
                if ( ( $personnageAretournee ) == NULL ) {
                    $personnageAretournee = $personnage;
                } else {
                    if ( $personnage->getNiveau () < $personnageAretournee->getNiveau () ) {
                        $personnageAretournee = $personnage;
                    }
                }
            }
        }

        return $personnageAretournee;
    }

    /**
     * Retourne vrai si tous les personnages sont mort
     * @return bool
     */
    function allPersonnagesDead ()
    {
        $i = 0;
        do {
            $allDead = $this->_personnages[ $i ]->isDead ();
            $i++;
        } while ( $allDead && $i < $this->getNombrePersonnages () );

        return $allDead;
    }

    /**
     * @return int
     */
    function getNombrePersonnages ()
    {
        return count ( $this->_personnages );
    }

    /**
     * Ajout d'un pourcentage d'experience à tous les personnages
     *
     * @param $pourcentXP
     */
    function ajouterPourcentExperience ( $pourcentXP )
    {
        static::log ( "Ajout d'un pourcentage d'experience à tous les personnages de l'equipe d'id : " . $this->_id_equipe );
        foreach ( $this->_personnages as $personnage ) {
            $personnage->addPourcentExperience ( $pourcentXP );
        }
    }

    /**
     * Trie les personnages dans l'equipe (objet) par niveau
     * @throws Exception
     */
    function trierPersonnageParNiveau ()
    {
        $this->trierPersonnagePersonnalise ( "comparerNiveauPersonnage" );
    }

    /**
     * Trie les personnages dans l'equipe (objet) par $parametre
     *
     * @param $parametre
     *
     * @throws Exception
     */
    function trierPersonnagePersonnalise ( $parametre )
    {
        static::log ( "trie de l'equipe par $parametre sur l'equipe d'id : " . $this->_id_equipe );
        if ( usort ( $this->_personnages, array ( "Personnage", $parametre ) ) ) {
        } else {
            throw new Exception( "Le trie a échoué" );
        }
    }

    /**
     * Retourne les personnages vivants de l'équipe
     * @return array <Personnage>
     */
    function getPersonnageVivant ()
    {
        $personnagesVivant = array ();
        foreach ( $this->_personnages as $personnage ) {
            if ( !$personnage->isDead () ) {
                array_push ( $personnagesVivant, $personnage );
            }
        }

        return $personnagesVivant;
    }

    /**
     * Soigne l'equipe (utiliser par boutique)
     */
    function soignerEquipe ()
    {
        static::log ( "Soigne les hp et mp de tous les personnages de l'equipe d'id : " . $this->_id_equipe );
        foreach ( $this->_personnages as $personnage ) {
            $personnage->addHp ( $personnage->getHpMax () );
            $personnage->addMp ( $personnage->getMpMax () );
        }
    }

    /**
     * Rafraichit l'equipe depuis la base de données
     * retire de l'equipe les personnages ne fesant plus partie de l'equipe
     */
    function refresh ()
    {
        if ( self::$db_debug ) {
            static::log ( "ID :" . $this->getIdEquipe () . "\t | Mise à jour de l'equipe ... " );
        }
        foreach ( $this->_personnages as $personnage ) {
            $personnage->refresh ();
            if ( $personnage->getIdEquipe () != $this->_id_equipe ) {
                $key = array_search ( $personnage, $this->_personnages );
                array_splice ( $this->_personnages, $key, 1 );
            }
        }
    }

    /**
     * @return int
     */
    function getIdEquipe ()
    {
        return $this->_id_equipe;
    }

    public function getPersoIndiceActuel ()
    {
        return $this->getPersonnageIndice ( $this->getIndicePersoActuel () );
    }

    /**
     * @return int
     */
    public function getIndicePersoActuel ()
    {
        return $this->_indice_perso_actuel;
    }

    /**
     * @param int $indice_perso_actuel
     */
    public function setIndicePersoActuel ( $indice_perso_actuel )
    {
        $this->_indice_perso_actuel = $indice_perso_actuel;
    }

    public function incrementerIndicePersoActuel ()
    {
        $i = 0;
        while ( $this->getPersonnageIndice ( $this->_indice_perso_actuel )->isDead () && !$this->allPersonnagesDead () || $i == 0 ) {
            if ( $this->_indice_perso_actuel < count ( $this->_personnages ) - 1 ) {
                $this->_indice_perso_actuel++;
            } else {
                $this->_indice_perso_actuel = 0;
            }
            $i++;
        }
    }
}
