<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "../objects/modele_attaque.php";
require_once MOD_BPATH . DIR_SEP . "../objects/modele_niveau.php";


class Personnage extends DBMapper
{

    static           $CARAC_ADD_FOR_UPPING = array (
            'hp_max'    => 10,
            'mp_max'    => 5,
            'puissance' => 3,
            'defense'   => 1
    );
    static           $id_personnage_serial = 9000;// IL ne faut pas dépasser 9000 persos
    protected static $DEBUG                = TRUE;
    protected static $log;
    protected        $_id_personnage;
    protected        $_nom;
    protected        $_element;
    protected        $_niveau;
    protected        $_experience;
    protected        $_attaques;
    protected        $_hp;
    protected        $_hp_max;
    protected        $_mp;
    protected        $_mp_max;
    protected        $_puissance;
    protected        $_defense;// retourne TRUE si present dans BD, FALSE dans le cas contraire
    //TODO Il faut désactiver les requetes vers la BDD if $_in_BD == FALSE
    // exemple : addHP()
    protected $_id_equipe;
    protected $_in_BD;

    /** Constructeur ; vérifie si $personnageElement est correcte
     *
     * @param $personnageElement
     *
     * @throws Exception
     */
    protected function __construct ( $personnageElement )
    {
        if ( $personnageElement[ 'id_personnage' ] >= 0 ) {
            $this->_id_personnage = $personnageElement[ 'id_personnage' ];
        } else {
            throw new Exception( 'id personnage incorrecte' );
        }
        if ( $personnageElement[ 'nom' ] != NULL ) {
            $this->_nom = $personnageElement[ 'nom' ];
        } else {
            throw new Exception( 'nom incorrecte' );
        }
        if ( $personnageElement[ 'element' ] > 0 ) {
            $this->_element = $personnageElement[ 'element' ];
        } else {
            throw new Exception( 'element incorrecte' );
        }
        if ( $personnageElement[ 'niveau' ] >= 0 ) {
            $this->_niveau = $personnageElement[ 'niveau' ];
        } else {
            throw new Exception( 'Niveau incorrecte' );
        }
        if ( $personnageElement[ 'experience' ] >= 0 ) {
            $this->_experience = $personnageElement[ 'experience' ];
        } else {
            throw new Exception( 'Experience incorrecte' );
        }
        //Convertit un String en liste d'Attaque
        if ( $personnageElement[ 'attaques' ] != NULL ) {
            $listesIdAttaques                = explode ( ";", $personnageElement[ 'attaques' ] );
            $personnageElement[ 'attaques' ] = array ();
            foreach ( $listesIdAttaques as $id_attaque ) {
                array_push ( $personnageElement[ 'attaques' ], new Attaque( $id_attaque ) );
            }
            $this->_attaques = $personnageElement[ 'attaques' ];
        } else {
            throw new Exception( 'attaques incorrecte' );
        }
        if ( $personnageElement[ 'hp' ] >= 0 ) {
            $this->_hp = $personnageElement[ 'hp' ];
        } else {
            throw new Exception( 'hp incorrecte' );
        }
        if ( $personnageElement[ 'hp_max' ] > 0 ) {
            $this->_hp_max = $personnageElement[ 'hp_max' ];
        } else {
            throw new Exception( 'hp_max incorrecte' );
        }
        if ( $personnageElement[ 'mp' ] >= 0 ) {
            $this->_mp = $personnageElement[ 'mp' ];
        } else {
            throw new Exception( 'mp incorrecte' );
        }
        if ( $personnageElement[ 'mp_max' ] >= 0 ) {
            $this->_mp_max = $personnageElement[ 'mp_max' ];
        } else {
            throw new Exception( 'mp_max incorrecte' );
        }
        if ( $personnageElement[ 'puissance' ] >= 0 ) {
            $this->_puissance = $personnageElement[ 'puissance' ];
        } else {
            throw new Exception( 'puissance incorrecte' );
        }
        if ( $personnageElement[ 'defense' ] >= 0 ) {
            $this->_defense = $personnageElement[ 'defense' ];
        } else {
            throw new Exception( 'defense incorrecte' );
        }
        if ( !isset( $personnageElement[ 'id_equipe' ] ) ) {
            $this->_id_equipe = NULL;
        } else {
            $this->_id_equipe = $personnageElement[ 'id_equipe' ];
        }
        if ( !isset( $personnageElement[ 'in_BD' ] ) ) {
            $this->_in_BD = FALSE;
        } else {
            $this->_in_BD = $personnageElement[ 'in_BD' ];
        }
        if ( self::$DEBUG ) {
            self::log ( "Construction personnage : " . $this->__toString () );
        }
    }

    static function log ( $message, $nomfichier = 'personnage' )
    {
        if ( !isset( self::$log ) ) {
            self::$log = new Logger( "./logs" );
            self::$log->log ( 'erreurs', $nomfichier, "___________________________________________", Logger::GRAN_MONTH );
        }
        self::$log->log ( 'erreurs', $nomfichier, $message, Logger::GRAN_MONTH );
        self::$log->log ( 'erreurs', 'statistique_personnage', $message, Logger::GRAN_MONTH );
    }

    function __toString ()
    {
        return 'ID:' . $this->_id_personnage . '; nom : ' . $this->_nom . '; lvl : ' . $this->_niveau;
    }

    /** Creer un personnage et le rajoute dans la base de données
     *
     * @param $niveau
     *
     * @return Personnage
     */
    static function createPersonnageForBD ( $niveau = 1 )
    {
        // Creation du personnage
        $personnageAvantBD = self::createPersonnage ( $niveau );
        // Ici on détermine l'identifiant personnage max +1 afin d'en créer un nouveau
        $resultat      = Personnage::requeteFromBD ( "SELECT max(id_personnage) FROM personnage" );
        $id_personnage = $resultat[ 0 ][ 0 ] + 1;
        //On creer un tableau contenant toutes les valeurs nécessaire pour l'ajout à la BD
        $personnageElement   = $personnageAvantBD->getPersonnage ();
        $elementPersonnageBD = array (
                'id_personnage' => $id_personnage,
                'nom'           => "Chausson " . $id_personnage,
                'element'       => $personnageElement[ 'element' ],
                'niveau'        => $personnageElement[ 'niveau' ],
                'experience'    => $personnageElement[ 'experience' ],
                'attaques'      => $personnageAvantBD->getIdAttaques (),
                'hp'            => $personnageElement[ 'hp' ],
                'hp_max'        => $personnageElement[ 'hp_max' ],
                'mp'            => $personnageElement[ 'mp' ],
                'mp_max'        => $personnageElement[ 'mp_max' ],
                'puissance'     => $personnageElement[ 'puissance' ],
                'defense'       => $personnageElement[ 'defense' ],
        );
        //ICI on créer le personnage dans la base de donnée
        Personnage::requeteFromBD (
                "INSERT INTO personnage VALUES(:id_personnage, :nom, :element, :niveau, :experience, :attaques, :hp, :hp_max, :mp, :mp_max, :puissance, :defense, NULL)",
                $elementPersonnageBD );
        $elementPersonnageBD[ 'in_BD' ] = TRUE;

        // On créer l'objet Personnage
        return new Personnage( $elementPersonnageBD );
    }

    /** Creer un personnage
     *
     * @param $niveau
     *
     * @return Personnage
     */
    static function createPersonnage ( $niveau, $nom="Monster" )
    {
        //ICI on récupère l'experience du niveau
        $resultat   = Personnage::requeteFromBD (
                "SELECT DISTINCT experience FROM niveau WHERE niveau = :niveau", array (
                        'niveau' => $niveau
                ) );
        $experience = $resultat[ 0 ][ 'experience' ];
        // Definition des caracteristique correspondant au niveau du personnage
        $caracNiveau = array (
                'experience' => $experience,
                'hp_max'     => self::$CARAC_ADD_FOR_UPPING[ 'hp_max' ] * $niveau,
                'mp_max'     => self::$CARAC_ADD_FOR_UPPING[ 'mp_max' ] * $niveau,
                'puissance'  => self::$CARAC_ADD_FOR_UPPING[ 'puissance' ] * $niveau,
                'defense'    => self::$CARAC_ADD_FOR_UPPING[ 'defense' ] * $niveau
        );
        // Creation de la liste des attaques ($listeAttaques)
        $listeAttaques = array ( new Attaque( 1 ), new Attaque( 2 ), new Attaque( 3 ) );
        // Creation d'une liste des id des attaques ($listeAttaquesString)
        $listeAttaquesString = "";
        $i                   = 0;
        foreach ( $listeAttaques as $attaques ) {
            if ( $i == 0 ) {
                $listeAttaquesString = $attaques->getIdAttaque ();
            } else {
                $listeAttaquesString = $listeAttaquesString . ";" . $attaques->getIdAttaque ();
            }
            $i++;
        }
        self::$id_personnage_serial++;
        // On prépare les éléments pour la création du Personnage
        $personnageElement = array (
                'id_personnage' => self::$id_personnage_serial,
                'nom'           => $nom,
                'element'       => rand ( 1, 4 ),// ICI on peut modifier l'élement du personnage par défault; ici aleatoire
                'niveau'        => $niveau,
                'experience'    => $caracNiveau[ 'experience' ],
                'attaques'      => $listeAttaquesString,
                'hp'            => $caracNiveau[ 'hp_max' ],
                'hp_max'        => $caracNiveau[ 'hp_max' ],
                'mp'            => $caracNiveau[ 'mp_max' ],
                'mp_max'        => $caracNiveau[ 'mp_max' ],
                'puissance'     => $caracNiveau[ 'puissance' ],
                'defense'       => $caracNiveau[ 'defense' ],
                'in_BD'         => FALSE
        );
        if ( self::$DEBUG ) {
            self::log ( "Creation personnage : " . "..." );
        }

        // On créer l'objet Personnage
        return new Personnage( $personnageElement );
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
    private static function requeteFromBD ( $requete, $donnees = array () )
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
        if ( self::$DEBUG ) {
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
            self::log ( $requete . " : \t $infos", "requete_personnage" );
        }

        return $resultat;
    }

    /** Retourne les éléments du personnage
     * @return array
     */
    function getPersonnage ()
    {
        return array (
                'id_personnage' => $this->_id_personnage,
                'nom'           => $this->_nom,
                'element'       => $this->_element,
                'niveau'        => $this->_niveau,
                'experience'    => $this->_experience,
                'attaques'      => $this->_attaques,
                'hp'            => $this->_hp,
                'hp_max'        => $this->_hp_max,
                'mp'            => $this->_mp,
                'mp_max'        => $this->_mp_max,
                'puissance'     => $this->_puissance,
                'defense'       => $this->_defense,
                'id_equipe'     => $this->_id_equipe,
                'in_BD'         => $this->_in_BD
        );
    }

    /** Recupère la liste des attaques du personnage
     * Renvoie les id des attaques séparé par un ";"
     * @return string
     */
    function getIdAttaques ()
    {
        // Creation d'une liste des id des attaques ($listeAttaquesString)
        $listeAttaquesString = "";
        $i                   = 0;
        foreach ( $this->_attaques as $attaques ) {
            if ( $i == 0 ) {
                $listeAttaquesString = $attaques->getIdAttaque ();
            } else {
                $listeAttaquesString = $listeAttaquesString . ";" . $attaques->getIdAttaque ();
            }
            $i++;
        }

        return $listeAttaquesString;
    }

    /** Recupere en fonction de l'id personnage le Personnage correspondant
     *
     * @param $id_personnage
     *
     * @return Personnage
     */
    static function getPersonnageFromBD ( $id_personnage )
    {
        //ICI on récupère les informations du personnage
        $resultat                     = Personnage::requeteFromBD (
                "SELECT DISTINCT * FROM personnage WHERE id_personnage = :id_personnage", array (
                        'id_personnage' => $id_personnage
                ) );
        $personnageElement            = $resultat[ 0 ];
        $personnageElement[ 'in_BD' ] = TRUE;

        return new Personnage( $personnageElement );
    }

    /** Retourne    0 si ==
     *              1 si $p1 > $p2
     *              -1 si $p1 > $p2
     *
     * @param $p1
     * @param $p2
     *
     * @return int
     */
    static function comparerNiveauPersonnage ( $p1, $p2 )
    {
        if ( $p1->getNiveau () == $p2->getNiveau () ) {
            return 0;
        }

        return ( $p1->getNiveau () < $p2->getNiveau () ) ? -1 : 1;
    }

    /** Ajoute un pourentage d'experience au personnage
     *
     * @param $pourcentExperience
     *
     * @throws Exception
     */
    function addPourcentExperience ( $pourcentExperience )
    {
        if ( $pourcentExperience < 0 ) {
            throw new Exception( "pourentage d'experience négatif" );
        }
        $experienceRepresentant100Pourcent = Niveau::getXpNiveau ( $this->_niveau + 1 ) - Niveau::getXpNiveau ( $this->_niveau );
        $experience                        = $experienceRepresentant100Pourcent * ( $pourcentExperience / 100 );
        $this->addExperience ( round ( $experience ) );
    }

    /** Ajoute l'experience passé en paramètre au personnage, met à jour le niveau puis met à jours la base de données
     *
     * @param $experience
     *
     * @throws Exception
     */
    function addExperience ( $experience )
    {
        if ( $experience >= 0 ) {
            $this->_experience += $experience;
            //ICI on met à jour l'experience du personnage dans la bdd
            if ( $this->_in_BD ) {
                Personnage::requeteFromBD (
                        "UPDATE personnage SET experience = :experience WHERE id_personnage =:id_personnage", array (
                                'id_personnage' => $this->_id_personnage,
                                'experience'    => $this->_experience
                        ) );
            }
            if ( self::$DEBUG ) {
                self::log ( "ID :" . $this->_id_personnage . "\t | Ajout d'experience : " . ( $this->_experience - $experience ) . " + " . $experience . " = " . $this->_experience . "xp" );
            }
            // Si experience requise pour up, alors up
            //TODO Dans le cas ou l'experience est incorrecte, et donc qu'il faut pas monter un niveau mais en diminuer
            //cette boucle permet de monter plusieurs niveau dans le cas ou il ya beaucoup d'experience d'ajouté
            while ( Niveau::getNiveau ( $this->_experience ) > $this->_niveau ) {
                $this->upNiveau ();
            }
        } else {
            throw new Exception( 'experience ajouté négative impossible' );
        }
    }

    function upNiveau ()
    {
        if ( Niveau::getNiveau ( $this->_experience ) != $this->_niveau ) {
            $this->_niveau = Niveau::getNiveau ( $this->_experience );
            //ICI on met à jour le niveau du personnage dans la bdd
            if ( $this->_in_BD ) {
                Personnage::requeteFromBD (
                        "UPDATE personnage SET niveau = :niveau WHERE id_personnage =:id_personnage", array (
                                'id_personnage' => $this->_id_personnage,
                                'niveau'        => $this->_niveau
                        ) );
            }
            if ( self::$DEBUG ) {
                self::log ( "ID :" . $this->_id_personnage . "\t | Monter au niveau : " . $this->_niveau );
            }
            //On rajoute les caracs
            $this->addPuissance ( Personnage::$CARAC_ADD_FOR_UPPING[ 'puissance' ] );
            $this->addDefense ( Personnage::$CARAC_ADD_FOR_UPPING[ 'defense' ] );
            $this->addHpMax ( Personnage::$CARAC_ADD_FOR_UPPING[ 'hp_max' ] );
            $this->addMpMax ( Personnage::$CARAC_ADD_FOR_UPPING[ 'mp_max' ] );
        } else {
            throw new Exception( 'upNiveau() a été appellé alors qu\'il ne devrais pas y avoir de up' );
        }
    }

    function addPuissance ( $puissance )
    {
        if ( $puissance > 0 ) {
            $this->_puissance += $puissance;
            if ( $this->_in_BD ) {
                Personnage::requeteFromBD (
                        "UPDATE personnage SET puissance = :puissance WHERE id_personnage =:id_personnage", array (
                                'id_personnage' => $this->_id_personnage,
                                'puissance'     => $this->_puissance
                        ) );
            }
            if ( self::$DEBUG ) {
                self::log ( "ID :" . $this->_id_personnage . "\t | Ajout de puissance : " . ( $this->_puissance - $puissance ) . " + " . $puissance . " = " . $this->_puissance . " puissance" );
            }
        } else {
            throw new Exception( 'puissance à rajouté négative' );
        }
    }

    function addDefense ( $defense )
    {
        if ( $defense > 0 ) {
            $this->_defense += $defense;
            if ( $this->_in_BD ) {
                Personnage::requeteFromBD (
                        "UPDATE personnage SET defense = :defense WHERE id_personnage =:id_personnage", array (
                                'id_personnage' => $this->_id_personnage,
                                'defense'       => $this->_defense
                        ) );
            }
            if ( self::$DEBUG ) {
                self::log ( "ID :" . $this->_id_personnage . "\t | Ajout de defense : " . ( $this->_defense - $defense ) . " + " . $defense . " = " . $this->_defense . " defense" );
            }
        } else {
            throw new Exception( 'defense à rajouté négative' );
        }
    }

    /** Ajoute les Point de vie max au personnage
     *
     * @param $hp_max
     *
     * @throws Exception
     */
    function addHpMax ( $hp_max )
    {
        if ( $hp_max > 0 ) {
            $this->_hp_max = $hp_max + $this->_hp_max;
            //Lors du up, remise au max des hp
            $this->_hp = $this->_hp_max;
            if ( $this->_in_BD ) {
                Personnage::requeteFromBD (
                        "UPDATE personnage SET hp_max = :hp_max, hp = :hp_max WHERE id_personnage = :id_personnage", array (
                                'id_personnage' => $this->_id_personnage,
                                'hp_max'        => $this->_hp_max
                        ) );
            }
            if ( self::$DEBUG ) {
                self::log ( "ID :" . $this->_id_personnage . "\t | Ajout d'hp_max : " . ( $this->_hp_max - $hp_max ) . " + " . $hp_max . " = " . $this->_hp_max . "hp" );
            }
        } else {
            throw new Exception( 'hp_max à rajouté négative' );
        }
    }

    /** Ajoute les Point de magie max au personnage
     *
     * @param $mp_max
     *
     * @throws Exception
     */
    function addMpMax ( $mp_max )
    {
        if ( $mp_max > 0 ) {
            $this->_mp_max += $mp_max;
            //Lors du up, remise au max des mp
            $this->_mp = $this->_mp_max;
            if ( $this->_in_BD ) {
                Personnage::requeteFromBD (
                        "UPDATE personnage SET mp_max = :mp_max, mp = :mp_max WHERE id_personnage = :id_personnage", array (
                                'id_personnage' => $this->_id_personnage,
                                'mp_max'        => $this->_mp_max
                        ) );
            }
            if ( self::$DEBUG ) {
                self::log ( "ID :" . $this->_id_personnage . "\t | Ajout de mp_max : " . ( $this->_mp_max - $mp_max ) . " + " . $mp_max . " = " . $this->_mp_max . "mp" );
            }
        } else {
            throw new Exception( 'mp_max à rajouté négative' );
        }
    }

    /** Ajoute les Point de vie au personnage
     *
     * @param $hp
     */
    function addHp ( $hp )
    {
        $hp_actuel = $this->_hp;
        //Mise à jour des HP
        if ( $hp + $this->_hp > $this->_hp_max ) {
            $this->_hp = $this->_hp_max;
        } else {
            $this->_hp = $this->_hp + $hp;
        }
        //Mise à jour dans la BD
        if ( $this->_in_BD ) {
            Personnage::requeteFromBD (
                    "UPDATE personnage SET hp = :hp WHERE id_personnage = :id_personnage", array (
                            'id_personnage' => $this->_id_personnage,
                            'hp'            => $this->_hp
                    ) );
        }
        if ( self::$DEBUG ) {
            self::log ( "ID :" . $this->_id_personnage . "\t | Ajout d'hp : " . $hp_actuel . " + " . $hp . " = " . $this->_hp . "hp" );
        }
    }

    /** Ajoute les Point de magie au personnage
     *
     * @param $mp
     */
    function addMp ( $mp )
    {
        //Mise à jour des MP
        if ( $mp + $this->_mp > $this->_mp_max ) {
            $this->_mp = $this->_mp_max;
        } else {
            $this->_mp = $this->_mp + $mp;
        }
        //Mise à jour dans la BD
        if ( $this->_in_BD ) {
            Personnage::requeteFromBD (
                    "UPDATE personnage SET mp = :mp WHERE id_personnage =:id_personnage", array (
                            'id_personnage' => $this->_id_personnage,
                            'mp'            => $this->_mp
                    ) );
        }
        if ( self::$DEBUG ) {
            self::log ( "ID :" . $this->_id_personnage . "\t | Ajout de mp : " . ( $this->_mp - $mp ) . " + " . $mp . " = " . $this->_mp . "mp" );
        }
    }

    /** On inflige les dégats au personnage:
     * le personnage réduit les dégats de manière
     * aléatoire entre 1 et $this->defense * $degats
     *
     * @param $degats
     *
     * @throws Exception
     */
    function subirDegats ( $degats )
    {
        if ( $degats >= 0 ) {
            if ( $this->_defense == 0 ) {
                $this->_defense = 1;
            }
            if ( self::$DEBUG ) {
                self::log ( "ID :" . $this->_id_personnage . "\t | Degats à infligés avant reduction : " . $degats . "hp" );
            }
            $degats = $degats - ( $this->_defense * rand ( 0.8, 1.2 ) );
            if ( $degats < 1 ) {
                $degats = 1;
            }
            $degats = round ( $degats );
            $this->retirerHP ( $degats );
            //echo "Degats infligés : " . $degats . ".<br />";
        } else {
            throw new Exception( 'degats infligé négatif' );
        }
    }

    /** Retire une valeur brute de point de vie
     * Met à jour la BD si $in_BD == VRAI
     *
     * @param $degats
     */
    function retirerHP ( $degats )
    {
        $degats = round ( $degats );
        if ( $this->_hp - $degats < 0 ) {
            $this->_hp = 0;
        } else {
            $this->_hp = $this->_hp - $degats;
        }
        //Mise à jour dans la BD
        if ( $this->_in_BD ) {
            Personnage::requeteFromBD (
                    "UPDATE personnage SET hp = :hp WHERE id_personnage =:id_personnage", array (
                            'id_personnage' => $this->_id_personnage,
                            'hp'            => $this->_hp
                    ) );
        }
        if ( self::$DEBUG ) {
            self::log ( "ID :" . $this->_id_personnage . "\t | Retrait d'hp : " . ( $this->_hp + $degats ) . " - " . $degats . " = " . $this->_hp . "hp" );
            if ( $this->isDead () ) {
                self::log ( "ID :" . $this->_id_personnage . "\t | Mort ..." );
            }
        }
    }

    /**
     * @return bool
     */
    function isDead ()
    {
        return $this->_hp == 0;
    }

    /** retourne les degats de l'attaque d'indice indiqué
     * met à jour les MP
     *
     * @param $attaqueIndice
     *
     * @return mixed
     * @throws Exception
     */
    function attaquer ( $attaqueIndice )
    {
        if ( $this->_attaques[ $attaqueIndice ] == NULL ) {
            throw new Exception( 'Indice attaque incorrecte' );
        }
        $attaque = $this->_attaques[ $attaqueIndice ];
        // On retire les MP ; Exception si pas suffisament de MP
        $this->retirerMP ( $attaque->getMpUsed () );
        // Algorithme de calcul de degats
        $degatsInfliges = ( $this->_niveau * 0.4 + 2 ) * $this->_puissance / rand ( 8, 12 );
        if ( $degatsInfliges == 0 ) {
            $degatsInfliges++;
        }
        //Ancienne version :     $degatsInfliges = ($this->_puissance) * ($attaque->getDegats());
        $degatsInfliges = round ( $degatsInfliges );
        if ( self::$DEBUG ) {
            self::log ( "ID :" . $this->_id_personnage . "\t | Attaque " . $attaque->getIdAttaque () . " infligeant " . $degatsInfliges . "hp" );
        }

        return $degatsInfliges;
    }

    /** Retire une valeur brute de point de magie
     *
     * @param $mp_used
     *
     * @throws Exception
     */
    function retirerMP ( $mp_used )
    {
        if ( $this->_mp - $mp_used < 0 ) {
            throw new Exception( 'Nombre de MP non sufisant: ' . $this->_mp . 'MP restant / ' . $mp_used . 'MP Necessaire' );
        } else {
            $this->_mp = $this->_mp - $mp_used;
        }
        //Mise à jour dans la BD
        if ( $this->_in_BD ) {
            Personnage::requeteFromBD (
                    "UPDATE personnage SET mp = :mp WHERE id_personnage =:id_personnage", array (
                            'id_personnage' => $this->_id_personnage,
                            'mp'            => $this->_mp
                    ) );
        }
        if ( self::$DEBUG ) {
            self::log ( "ID :" . $this->_id_personnage . "\t | Retrait de mp : " . ( $this->_mp + $mp_used ) . " - " . $mp_used . " = " . $this->_mp . "mp" );
        }
    }

    /**
     * @return array(Attaques)
     */
    function getAttaques ()
    {
        return $this->_attaques;
    }

    /** retourne l'experience manquant pour monter le niveau suivant
     *
     *
     * @return mixed
     */
    function getXpManquantPourUp ()
    {
        return Niveau::getXpManquantPourUp ( $this->_experience );
    }

    /** De 0 à 99% xp réalisé
     * @return mixed
     */
    function getPourcentXp ()
    {
        return Niveau::getPourcentXp ( $this->_experience );
    }

    /**
     * @return Niveau (int)
     */
    function getNiveau ()
    {
        return $this->_niveau;
    }

    /**
     *
     */
    function refresh ()
    {
        //TODO
    }

    /**
     * @return int id_personnage
     */
    function getIdPersonnage ()
    {
        return $this->_id_personnage;
    }

    function afficher ()
    {
        echo '<table border="1">
    <tr>
        <th> ID Perso</th>
        <th> Nom</th>
        <th> Element</th>
        <th> Niveau</th>
        <th> Exp</th>
        <th> attaques</th>
        <th> HP</th>
        <th> MP</th>
        <th> Puissance</th>
        <th> Defense</th>
        <th> ID Equipe</th>
    </tr>';
        echo "<tr>\n";
        echo "<td>" . $this->_id_personnage . "</td>\n";
        echo "<td>" . $this->_nom . "</td>\n";
        echo "<td>" . $this->_element . "</td>\n";
        echo "<td>" . $this->_niveau . "</td>\n";
        echo "<td>" . $this->_experience . "</td>\n";
        echo "<td>" . $this->getIdAttaques () . "</td>\n";
        echo "<td>" . $this->_hp . "</td>\n";
        echo "<td>" . $this->_mp . "</td>\n";
        echo "<td>" . $this->_puissance . "</td>\n";
        echo "<td>" . $this->_defense . "</td>\n";
        echo "<td>" . $this->_id_equipe . "</td>\n";
        echo "</tr>";
        echo "</table>";
    }

    /**
     * @return int id_equipe
     */
    function getIdEquipe ()
    {
        return $this->_id_equipe;
    }

    /**
     * @param mixed $id_equipe
     */
    function setIDEquipe ( $id_equipe = NULL )
    {
        //Changement id_equipe du personnage
        //Mise à jour dans la BD
        if ( $this->_in_BD ) {
            Personnage::requeteFromBD (
                    "UPDATE personnage SET id_equipe = :id_equipe WHERE id_personnage= :id_personnage", array (
                            'id_equipe'     => $id_equipe,
                            'id_personnage' => $this->_id_personnage
                    ) );
        }
        $this->_id_equipe = $id_equipe;
    }

    /** Retourne l'identifiant element du personnage
     * @return int
     */
    public function getElement ()
    {
        return $this->_element;
    }

    //TODO a supprimer, retourneaffichage c mieux
    function retourneAffichagePersonnage ()
    {
        $rep  = "img/avatar/";
        $text = '<div class="personnage">' .
                '<img src="' . Element::getIcone ( $this->_element ) . '" alt="' . Element::getNom ( $this->_element ) . '"/> <br/>'
                . '<img class="avatarPerso" src="' . $rep . $this->_element . ".jpg" . '" alt="' . $this->_nom . '"/> <br/>'
                . $this->_nom . ' (Level ' . $this->_niveau . ') <br/>'
                . 'Experience : ' . $this->_experience . '/' . Niveau::getXpNiveau ( $this->_niveau + 1 ) . ' <br/>'
                . 'HP : ' . $this->_hp . ' / ' . $this->_hp_max . '<br/>'
                . '<progress value="' . $this->_hp . '" min ="0" max="' . $this->_hp_max . '"></progress> <br/>'
                . 'MP : ' . $this->_mp . ' / ' . $this->_mp_max . '<br/>'
                . '<progress value="' . $this->_mp . '" min ="0" max="' . $this->_mp_max . '"></progress> <br/>'
                . 'Puissance : ' . $this->_puissance . '<br/>'
                . 'Defense : ' . $this->_defense . '<br/>'
                . '</div>';
        return $text;
    }

    //TODO a supprimer, retourneaffichage c mieux
    function retourneAffichagePersonnageAvecAttaque ()
    {
        $rep  = "img/avatar/";
        $text = '<div class="personnage">' .
                '<img src="' . Element::getIcone ( $this->_element ) . '" alt="' . Element::getNom ( $this->_element ) . '"/> <br/>'
                . '<img src="' . $rep . $this->_element . ".jpg" . '" alt="' . $this->_nom . '"/> <br/>'
                . $this->_nom . ' (level ' . $this->_niveau . ') <br/>'
                . 'experience : ' . $this->_experience . '/' . Niveau::getXpNiveau ( $this->_niveau + 1 ) . ' <br/>'
                . 'HP : ' . $this->_hp . ' / ' . $this->_hp_max . '<br/>'
                . '<progress value="' . $this->_hp . '" min ="0" max="' . $this->_hp_max . '"></progress> <br/>'
                . 'MP : ' . $this->_mp . ' / ' . $this->_mp_max . '<br/>'
                . '<progress value="' . $this->_mp . '" min ="0" max="' . $this->_mp_max . '"></progress> <br/>'
                . 'Puissance : ' . $this->_puissance . '<br/>'
                . 'Defense : ' . $this->_defense . '<br/>';
        $i    = 1;
        $text = $text . '<form>';
        foreach ( $this->_attaques as $attaque ) {
            $text = $text . '<button class="attaque" name="attaque" value"' . $attaque->getIdAttaque () . '" >';
            $text = $text . $attaque->__toString () . '';
            $text = $text . '</button>';
            $i++;
        }
        $text = $text . '</form>';
        $text = $text . '</div>';

        return $text;
    }

    function afficherPersonnage ()
    {
        echo '<div class="personnage">';
        // Affichage icone element
        echo '<img src="' . Element::getIcone ( $this->_element ) . '" alt="' . Element::getNom ( $this->_element ) . '"/> <br/>';
        // Image:
        $rep = "img/avatar/";
        echo '<img src="' . $rep . $this->_element . ".jpg" . '" alt="' . $this->_nom . '"/> <br/>';
        // FIN IMAGE
        echo $this->_nom . ' (level ' . $this->_niveau . ') <br/>';
        echo 'experience : ' . $this->_experience . '/' . Niveau::getXpNiveau ( $this->_niveau + 1 ) . ' <br/>';//' ('.$this->getPourcentXp().'%) <br/>' ;
        echo 'HP : ' . $this->_hp . ' / ' . $this->_hp_max . '<br/>';
        echo '<progress value="' . $this->_hp . '" min ="0" max="' . $this->_hp_max . '"></progress> <br/>';
        echo 'MP : ' . $this->_mp . ' / ' . $this->_mp_max . '<br/>';
        echo '<progress value="' . $this->_mp . '" min ="0" max="' . $this->_mp_max . '"></progress> <br/>';
        echo 'Puissance : ' . $this->_puissance . '<br/>';
        echo 'Defense : ' . $this->_defense . '<br/>';
        foreach ( $this->_attaques as $attaque ) {
            $attaque->afficherAttaque ();
        }
        echo '</div>';
    }

    function afficherPersonnagePourAttaquer ()
    {
        echo '<div class="personnage">';
        // Image:
        $rep = "img/avatar/";
        echo '<img src="' . $rep . "1.jpg" . '" alt="' . $this->_nom . '"/> <br/>';
        // FIN IMAGE
        echo $this->_nom . ' (level ' . $this->_niveau . ') <br/>';
        echo 'HP : ' . $this->_hp . ' / ' . $this->_hp_max . '<br/>';
        echo '<progress value="' . $this->_hp . '" min ="0" max="' . $this->_hp_max . '"></progress> <br/>';
        echo 'MP : ' . $this->_mp . ' / ' . $this->_mp_max . '<br/>';
        echo '<progress value="' . $this->_mp . '" min ="0" max="' . $this->_mp_max . '"></progress> <br/>';
        echo 'Puissance : ' . $this->_puissance . '<br/>';
        echo 'Defense : ' . $this->_defense . '<br/>';
        $i = 1;
        echo '<form>';
        foreach ( $this->_attaques as $attaque ) {
            echo '<button class="attaque" name="attaque" value"' . $attaque->getIdAttaque () . '" >';
            echo $attaque->__toString () . '';
            echo '</button>';
            $i++;
        }
        echo '</form>';
        echo '</div>';
    }
}
