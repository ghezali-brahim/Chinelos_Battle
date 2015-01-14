<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "../objects/modele_attaque.php";
require_once MOD_BPATH . DIR_SEP . "../objects/modele_niveau.php";


class Personnage extends DBMapper
{

    static    $CARAC_ADD_FOR_UPPING = array (
            'hp_max'    => 10,
            'mp_max'    => 5,
            'puissance' => 3,
            'defense'   => 1
    );
    static    $id_personnage_serial = 9000;// IL ne faut pas dépasser 9000 persos
    protected $_id_personnage;
    protected $_nom;
    protected $_element;
    protected $_niveau;
    protected $_experience;
    protected $_attaques;
    protected $_hp;
    protected $_hp_max;
    protected $_mp;
    protected $_mp_max;
    protected $_puissance;
    protected $_defense;// retourne TRUE si present dans BD, FALSE dans le cas contraire
    protected $_indice_attaque_choisit;
    //TODO Il faut désactiver les requetes vers la BDD if $_in_BD == FALSE
    // exemple : addHP()
    protected $_id_equipe;
    protected $_in_BD;

    /**
     * Constructeur ; vérifie si $personnageElement est correcte
     *
     * @param $personnageElement : array
     */
    protected function __construct ( $personnageElement )
    {
        try {
            if ( $personnageElement[ 'id_personnage' ] >= 0 ) {
                $this->_id_personnage = $personnageElement[ 'id_personnage' ];//TODO
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
            if ( self::$db_debug ) {
                static::log ( "Construction " . __CLASS__ . " : " . $this->__toString () );
            }
            $this->_indice_attaque_choisit = 0;
        } catch ( Exception $exception ) {
            static::log ( "Constructeur : " . $exception, "exceptions_" . __CLASS__ );
            if ( self::$db_debug ) {
                print_r ( $exception );
            }
        }
    }

    function __toString ()
    {
        return 'ID:' . $this->getIdPersonnage () . '; nom : ' . $this->_nom . '; lvl : ' . $this->_niveau;
    }

    /**
     * Retourne l'identifiant personnage
     * @return int id_personnage
     */
    function getIdPersonnage ()
    {
        return $this->_id_personnage;
    }

    /**
     * Creer un personnage et le rajoute dans la base de données
     *
     * @param int  $niveau
     * @param null $id_equipe
     *
     * @return Personnage
     * @throws Exception
     */
    static function createPersonnageForBD ( $niveau = 1, $id_equipe = NULL )
    {
        // Creation du personnage
        $personnageAvantBD = self::createPersonnage ( $niveau );
        // Ici on détermine l'identifiant personnage max +1 afin d'en créer un nouveau
        $resultat      = static::requeteFromDB ( "SELECT max(id_personnage) FROM personnage" );
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
                'id_equipe'     => $id_equipe
        );
        //ICI on créer le personnage dans la base de donnée
        static::requeteFromDB (
                "INSERT INTO personnage VALUES(:id_personnage, :nom, :element, :niveau, :experience, :attaques, :hp, :hp_max, :mp, :mp_max, :puissance, :defense, :id_equipe)",
                $elementPersonnageBD );
        $elementPersonnageBD[ 'in_BD' ] = TRUE;

        // On créer l'objet Personnage
        return new Personnage( $elementPersonnageBD );
    }

    /**
     * Creer un personnage
     *
     * @param int    $niveau
     * @param string $nom
     *
     * @return Personnage
     * @throws Exception
     */
    static function createPersonnage ( $niveau, $nom = "Monster" )
    {
        if ( self::$db_debug ) {
            static::log ( "Creation personnage : " . "..." );
        }
        //ICI on récupère l'experience du niveau
        $resultat   = static::requeteFromDB (
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

        // On créer l'objet Personnage
        return new Personnage( $personnageElement );
    }

    /**
     * Retourne les éléments du personnage
     * @return array
     */
    function getPersonnage ()
    {
        return array (
                'id_personnage' => $this->getIdPersonnage (),
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

    /**
     * Recupère la liste des attaques du personnage
     * Renvoie les id des attaques séparé par un ";"
     * @return string
     */
    function getIdAttaques ()
    {
        // Creation d'une liste des id des attaques ($listeAttaquesString)
        $listeAttaquesString = "";
        $i                   = 0;
        foreach ( $this->getAttaques () as $attaques ) {
            if ( $i == 0 ) {
                $listeAttaquesString = $attaques->getIdAttaque ();
            } else {
                $listeAttaquesString = $listeAttaquesString . ";" . $attaques->getIdAttaque ();
            }
            $i++;
        }

        return $listeAttaquesString;
    }

    /**
     * Retourne la liste des attaques du personnage
     * @return array(Attaques)
     */
    function getAttaques ()
    {
        return $this->_attaques;
    }

    /**
     * Recupere en fonction de l'id personnage le Personnage correspondant
     *
     * @param $id_personnage
     *
     * @return Personnage
     */
    static function getPersonnageFromBD ( $id_personnage )
    {
        //ICI on récupère les informations du personnage
        $resultat                     = static::requeteFromDB (
                "SELECT DISTINCT * FROM personnage WHERE id_personnage = :id_personnage", array (
                        'id_personnage' => $id_personnage
                ) );
        $personnageElement            = $resultat[ 0 ];
        $personnageElement[ 'in_BD' ] = TRUE;

        return new Personnage( $personnageElement );
    }

    /**
     * Retourne    0 si ==
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

    /**
     * Ajoute un pourentage d'experience au personnage
     *
     * @param $pourcentExperience
     *
     * @throws Exception
     */
    function addPourcentExperience ( $pourcentExperience )
    {
        if ( $pourcentExperience < 0 ) {
            throw new Exception( "pourcentage d'experience négatif" );
        }
        $experienceRepresentant100Pourcent = Niveau::getXpNiveau ( $this->_niveau + 1 ) - Niveau::getXpNiveau ( $this->_niveau );
        $experience                        = $experienceRepresentant100Pourcent * ( $pourcentExperience / 100 );
        $this->addExperience ( round ( $experience ) );
    }

    /**
     * Ajoute l'experience passé en paramètre au personnage, met à jour le niveau puis met à jours la base de données
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
                static::requeteFromDB (
                        "UPDATE personnage SET experience = :experience WHERE id_personnage =:id_personnage", array (
                                'id_personnage' => $this->getIdPersonnage (),
                                'experience'    => $this->_experience
                        ) );
            }
            if ( self::$db_debug ) {
                static::log ( "ID :" . $this->getIdPersonnage () . "\t | Ajout d'experience : " . ( $this->_experience - $experience ) . " + " . $experience . " = " . $this->_experience . "xp" );
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

    /**
     * Fait monter le niveau du personnage puis lui rajoute les caractéristiques supplémentaires
     * @throws Exception
     */
    function upNiveau ()
    {
        if ( Niveau::getNiveau ( $this->_experience ) != $this->_niveau ) {
            $this->_niveau = Niveau::getNiveau ( $this->_experience );
            //ICI on met à jour le niveau du personnage dans la bdd
            if ( $this->_in_BD ) {
                static::requeteFromDB (
                        "UPDATE personnage SET niveau = :niveau WHERE id_personnage =:id_personnage", array (
                                'id_personnage' => $this->getIdPersonnage (),
                                'niveau'        => $this->_niveau
                        ) );
            }
            if ( self::$db_debug ) {
                static::log ( "ID :" . $this->getIdPersonnage () . "\t | Monter au niveau : " . $this->_niveau );
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

    /**
     * Ajoute de la puissance au personnage
     *
     * @param $puissance
     *
     * @throws Exception si puissance <= 0
     */
    function addPuissance ( $puissance )
    {
        if ( $puissance > 0 ) {
            $this->_puissance += $puissance;
            if ( $this->_in_BD ) {
                static::requeteFromDB (
                        "UPDATE personnage SET puissance = :puissance WHERE id_personnage =:id_personnage", array (
                                'id_personnage' => $this->getIdPersonnage (),
                                'puissance'     => $this->_puissance
                        ) );
            }
            if ( self::$db_debug ) {
                static::log ( "ID :" . $this->getIdPersonnage () . "\t | Ajout de puissance : " . ( $this->_puissance - $puissance ) . " + " . $puissance . " = " . $this->_puissance . " puissance" );
            }
        } else {
            throw new Exception( 'puissance à rajouté négative' );
        }
    }

    /**
     * Ajoute de la defense au personnage
     *
     * @param $defense
     *
     * @throws Exception si defense <= 0
     */
    function addDefense ( $defense )
    {
        if ( $defense > 0 ) {
            $this->_defense += $defense;
            if ( $this->_in_BD ) {
                static::requeteFromDB (
                        "UPDATE personnage SET defense = :defense WHERE id_personnage =:id_personnage", array (
                                'id_personnage' => $this->getIdPersonnage (),
                                'defense'       => $this->_defense
                        ) );
            }
            if ( self::$db_debug ) {
                static::log ( "ID :" . $this->getIdPersonnage () . "\t | Ajout de defense : " . ( $this->_defense - $defense ) . " + " . $defense . " = " . $this->_defense . " defense" );
            }
        } else {
            throw new Exception( 'defense à rajouté négative' );
        }
    }

    /**
     * Ajoute les Point de vie max au personnage
     *
     * @param $hp_max
     *
     * @throws Exception si hp_max <= 0
     */
    function addHpMax ( $hp_max )
    {
        if ( $hp_max > 0 ) {
            $this->_hp_max = $hp_max + $this->_hp_max;
            //Lors du up, remise au max des hp
            $this->_hp = $this->_hp_max;
            if ( $this->_in_BD ) {
                static::requeteFromDB (
                        "UPDATE personnage SET hp_max = :hp_max, hp = :hp_max WHERE id_personnage = :id_personnage", array (
                                'id_personnage' => $this->getIdPersonnage (),
                                'hp_max'        => $this->_hp_max
                        ) );
            }
            if ( self::$db_debug ) {
                static::log ( "ID :" . $this->getIdPersonnage () . "\t | Ajout d'hp_max : " . ( $this->_hp_max - $hp_max ) . " + " . $hp_max . " = " . $this->_hp_max . "hp" );
            }
        } else {
            throw new Exception( 'hp_max à rajouté négative' );
        }
    }

    /**
     * Ajoute les Point de magie max au personnage
     *
     * @param $mp_max
     *
     * @throws Exception si mp_max <= 0
     */
    function addMpMax ( $mp_max )
    {
        if ( $mp_max > 0 ) {
            $this->_mp_max += $mp_max;
            //Lors du up, remise au max des mp
            $this->_mp = $this->_mp_max;
            if ( $this->_in_BD ) {
                static::requeteFromDB (
                        "UPDATE personnage SET mp_max = :mp_max, mp = :mp_max WHERE id_personnage = :id_personnage", array (
                                'id_personnage' => $this->getIdPersonnage (),
                                'mp_max'        => $this->_mp_max
                        ) );
            }
            if ( self::$db_debug ) {
                static::log ( "ID :" . $this->getIdPersonnage () . "\t | Ajout de mp_max : " . ( $this->_mp_max - $mp_max ) . " + " . $mp_max . " = " . $this->_mp_max . "mp" );
            }
        } else {
            throw new Exception( 'mp_max à rajouté négative' );
        }
    }

    /**
     * Ajoute les Point de vie au personnage
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
            static::requeteFromDB (
                    "UPDATE personnage SET hp = :hp WHERE id_personnage = :id_personnage", array (
                            'id_personnage' => $this->getIdPersonnage (),
                            'hp'            => $this->_hp
                    ) );
        }
        if ( self::$db_debug ) {
            static::log ( "ID :" . $this->getIdPersonnage () . "\t | Ajout d'hp : " . $hp_actuel . " + " . $hp . " = " . $this->_hp . "hp" );
        }
    }

    /**
     * Ajoute les Point de magie au personnage
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
            static::requeteFromDB (
                    "UPDATE personnage SET mp = :mp WHERE id_personnage =:id_personnage", array (
                            'id_personnage' => $this->getIdPersonnage (),
                            'mp'            => $this->_mp
                    ) );
        }
        if ( self::$db_debug ) {
            static::log ( "ID :" . $this->getIdPersonnage () . "\t | Ajout de mp : " . ( $this->_mp - $mp ) . " + " . $mp . " = " . $this->_mp . "mp" );
        }
    }

    /**
     * On inflige les dégats au personnage:
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
            if ( self::$db_debug ) {
                static::log ( "ID :" . $this->getIdPersonnage () . "\t | Degats à infligés avant reduction : " . $degats . "hp" );
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

    /**
     * Retire une valeur brute de point de vie
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
            static::requeteFromDB (
                    "UPDATE personnage SET hp = :hp WHERE id_personnage =:id_personnage", array (
                            'id_personnage' => $this->getIdPersonnage (),
                            'hp'            => $this->_hp
                    ) );
        }
        if ( self::$db_debug ) {
            static::log ( "ID :" . $this->getIdPersonnage () . "\t | Retrait d'hp : " . ( $this->_hp + $degats ) . " - " . $degats . " = " . $this->_hp . "hp" );
            if ( $this->isDead () ) {
                static::log ( "ID :" . $this->getIdPersonnage () . "\t | Mort ..." );
            }
        }
    }

    /**
     * Retourne vrai si le personnage est mort
     * @return bool
     */
    function isDead ()
    {
        return $this->_hp == 0;
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
     *  Remet à jour le personnage à partir de la base de données
     */
    function refresh () //TODO A FAIRE ET A VERIFIER
    {
        if ( self::$db_debug ) {
            static::log ( "ID :" . $this->getIdPersonnage () . "\t | Mise à jour du personnage ... " );
        }
        if ( $this->_in_BD ) {
            $resultat          = static::requeteFromDB (
                    "SELECT DISTINCT * FROM personnage WHERE id_personnage = :id_personnage", array (
                            'id_personnage' => $this->getIdPersonnage ()
                    ) );
            $personnageElement = $resultat[ 0 ];
            $this->_nom        = $personnageElement[ 'nom' ];
            $this->_element    = $personnageElement[ 'element' ];
            $this->_niveau     = $personnageElement[ 'niveau' ];
            $this->_experience = $personnageElement[ 'experience' ];
            $this->_hp         = $personnageElement[ 'hp' ];
            $this->_hp_max     = $personnageElement[ 'hp_max' ];
            $this->_mp         = $personnageElement[ 'mp' ];
            $this->_mp_max     = $personnageElement[ 'mp_max' ];
            $this->_puissance  = $personnageElement[ 'puissance' ];
            $this->_defense    = $personnageElement[ 'defense' ];
            $this->_id_equipe  = $personnageElement[ 'id_equipe' ];
        }
    }

    /**
     * Obsolete : Affichage du personnage sous forme de tableau
     */
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
        echo "<td>" . $this->getIdPersonnage () . "</td>\n";
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
     * Retourne l'id equipe du personnage
     * @return int id_equipe
     */
    function getIdEquipe ()
    {
        return $this->_id_equipe;
    }

    /**
     * Definit l'id de l'equipe du personnage
     *
     * @param mixed $id_equipe
     */
    function setIDEquipe ( $id_equipe = NULL )
    {
        //Changement id_equipe du personnage
        //Mise à jour dans la BD
        if ( $this->_in_BD ) {
            static::requeteFromDB (
                    "UPDATE personnage SET id_equipe = :id_equipe WHERE id_personnage= :id_personnage", array (
                            'id_equipe'     => $id_equipe,
                            'id_personnage' => $this->getIdPersonnage ()
                    ) );
        }
        $this->_id_equipe = $id_equipe;
    }

    /**
     * Retourne l'affichage du Personnage sous forme de text html
     * @return string
     */
    function retourneAffichagePersonnage ()
    {
        $rep  = "img/avatar/";
        $text = '<div class="personnage">' .
                '<img src="' . Element::getIcone ( $this->_element ) . '" alt="' . Element::getNom ( $this->_element ) . '"/> <br/>'
                . '<img class="avatarPerso" src="' . $rep . $this->_element . ".jpg" . '" alt="' . $this->_nom . '"/> <br/>'
                . $this->_nom . ' (Level ' . $this->_niveau . ') <br/>'
                . 'Experience : ' . $this->_experience . '/' . Niveau::getXpNiveau ( $this->_niveau + 1 ) . ' <br/>'
                . 'HP : ' . $this->_hp . ' / ' . $this->_hp_max . '<br/>'
                . '<progress value="' . $this->_hp . '" max="' . $this->_hp_max . '"></progress> <br/>'
                . 'MP : ' . $this->_mp . ' / ' . $this->_mp_max . '<br/>'
                . '<progress value="' . $this->_mp . '"  max="' . $this->_mp_max . '"></progress> <br/>'
                . 'Puissance : ' . $this->_puissance . '<br/>'
                . 'Defense : ' . $this->_defense . '<br/>'
                . '</div>';

        return $text;
    }

    /**
     * Retourne l'affichage du Personnage sous forme de text html
     * On rajoute les boutons pour les attaques
     * @return string
     */
    function retourneAffichagePersonnageAvecAttaque ()
    {
        $rep  = "img/avatar/";
        $text = '<div class="personnage" style="background-color:#ffcf95;">' .
                '<img src="' . Element::getIcone ( $this->_element ) . '" alt="' . Element::getNom ( $this->_element ) . '"/> <br/>'
                . '<img src="' . $rep . $this->_element . ".jpg" . '" alt="' . $this->_nom . '"/> <br/>'
                . $this->_nom . ' (level ' . $this->_niveau . ') <br/>'
                . 'experience : ' . $this->_experience . '/' . Niveau::getXpNiveau ( $this->_niveau + 1 ) . ' <br/>'
                . 'HP : ' . $this->_hp . ' / ' . $this->_hp_max . '<br/>'
                . '<progress value="' . $this->_hp . '" max="' . $this->_hp_max . '"></progress> <br/>'
                . 'MP : ' . $this->_mp . ' / ' . $this->_mp_max . '<br/>'
                . '<progress value="' . $this->_mp . '" max="' . $this->_mp_max . '"></progress> <br/>'
                . 'Puissance : ' . $this->_puissance . '<br/>'
                . 'Defense : ' . $this->_defense . '<br/>';
        $text .= '<div class="attaque" style="background-color: #F0AD4E">';
        foreach ( $this->_attaques as $attaque ) {
            $text = $text . $attaque->__toString () . '<br>';
        }
        $text .= '</div>';
        $text = $text . '</div>';

        return $text;
    }

    /**
     * Retourne les hp max du personnage
     * @return int
     */
    public function getHpMax ()
    {
        return $this->_hp_max;
    }

    /**
     * Retourne les mp max du personnage
     * @return int
     */
    public function getMpMax ()
    {
        return $this->_mp_max;
    }

    /**
     * Inflige des degats à un personnage ennemi
     * en utilisant une attaque du personnage
     *
     * @param $personnageTarget
     *
     * @throws Exception
     */
    function attaquerPersonnage ( $personnageTarget )
    {
        if ( is_null ( $personnageTarget ) ) {
            throw new Exception( "Veuillez choisir un personnage à attaquer" );
        }
        if ( $this->isDead () ) {
            throw new Exception( "ID Perso : $this->_id_personnage ; de niveau : $this->_niveau || Impossible d'attaquer, vous êtes morts ..." );
        }
        $indice_attaque = $this->getIndiceAttaqueChoisit ();
        $degats         = $this->attaquer ( $indice_attaque );
        echo "Personnage :" . $this->__toString () . "\t | Attaque le personnage  : " . $personnageTarget->__toString () . " et inflige $degats hp.";
        if ( self::$db_debug ) {
            static::log ( "ID :" . $this->getIdPersonnage () . "\t | Attaque le personnage  : " . $personnageTarget->getIdPersonnage () . " et inflige $degats hp." );
        }
        $degats = $degats * Element::getRatioDegatElement ( $this->getElement (), $personnageTarget->getElement () );
        $personnageTarget->subirDegats ( $degats );
    }

    /**
     * @return int
     */
    public function getIndiceAttaqueChoisit ()
    {
        return $this->_indice_attaque_choisit;
    }

    /**
     * @param int $indice_attaque_choisit
     *
     * @throws Exception
     */
    public function setIndiceAttaqueChoisit ( $indice_attaque_choisit )
    {
        if ( array_key_exists ( $indice_attaque_choisit, $this->_attaques ) ) {
            $this->_indice_attaque_choisit = $indice_attaque_choisit;
        } else {
            throw new Exception( "Indice de l'attaque choisit non valide" );
        }
    }

    /**
     * retourne les degats de l'attaque d'indice indiqué
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
        if ( self::$db_debug ) {
            static::log ( "ID :" . $this->getIdPersonnage () . "\t | Attaque " . $attaque->getIdAttaque () . " infligeant " . $degatsInfliges . "hp" );
        }

        return $degatsInfliges;
    }

    /**
     * Retire une valeur brute de point de magie
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
            static::requeteFromDB (
                    "UPDATE personnage SET mp = :mp WHERE id_personnage =:id_personnage", array (
                            'id_personnage' => $this->getIdPersonnage (),
                            'mp'            => $this->_mp
                    ) );
        }
        if ( self::$db_debug ) {
            static::log ( "ID :" . $this->getIdPersonnage () . "\t | Retrait de mp : " . ( $this->_mp + $mp_used ) . " - " . $mp_used . " = " . $this->_mp . "mp" );
        }
    }

    /**
     * Retourne l'identifiant element du personnage
     * @return int
     */
    public function getElement ()
    {
        return $this->_element;
    }

    function getNombreAttaque ()
    {
        return count ( $this->_attaques );
    }

    /**
     * Retourne le personnage le plus haut level entre deux
     * @param $p1
     * @param $p2
     *
     * @return Personnage
     */
    static function getPersonnagePlusHL($p1, $p2){
        if(Personnage::comparerNiveauPersonnage($p1,$p2)>=0){
            return $p1;
        }else{
            return $p2;
        }
    }
}
