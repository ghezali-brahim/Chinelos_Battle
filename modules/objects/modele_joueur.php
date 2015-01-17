<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "../objects/modele_participant.php";


class Joueur extends Participant
{
    //Player
    public    $_date_derniere_refresh;
    protected $_id_user;
    protected $_username;
    protected $_argent;
    protected $_nombre_victoire;
    protected $_nombre_defaite;

    /**
     * On créer un Joueur à partir de la base de données; On verifie d'abbord si le joueur est connecté.
     */
    function __construct ()
    {
        if ( self::connectee () ) {
            $user     = unserialize ( $_SESSION[ 'user' ] );
            $resultat = static::requeteFromDB ( "SELECT id_user, username, argent,nombre_victoire, nombre_defaite FROM users WHERE username = :username AND id_user = :id_user", array (
                    'username' => $user->getUsername (),
                    'id_user'  => $user->getIdUser ()
            ) )[ 0 ];
            //Recuperation infos du joueur
            $this->_id_user         = $resultat[ 'id_user' ];
            $this->_username        = $resultat[ 'username' ];
            $this->_argent          = $resultat[ 'argent' ];
            $this->_nombre_victoire = $resultat[ 'nombre_victoire' ];
            $this->_nombre_defaite  = $resultat[ 'nombre_defaite' ];
            // Recuperation de la liste des équipes
            $listeEquipes   = static::requeteFromDB ( "SELECT id_equipe FROM equipe WHERE id_user = :id_user", array (
                    'id_user' => $this->_id_user
            ) );
            $this->_equipes = array ();
            // SI le compte n'a pas deux equipes, alors on lui créer les deux equipes
            if ( count ( $listeEquipes ) == 0 ) {
                Equipe::createTwoEquipeForBD ( $this->_id_user );
                // Recuperation de la liste des équipes
                $listeEquipes = static::requeteFromDB ( "SELECT id_equipe FROM equipe WHERE id_user = :id_user", array (
                        'id_user' => $this->_id_user
                ) );
            }
            foreach ( $listeEquipes as $id_equipe ) {
                $equipe = Equipe::createEquipeFromBD ( $id_equipe[ 'id_equipe' ] );
                array_push ( $this->_equipes, $equipe );
            }
        } else {
            exit ( 'vous n\'etes pas connecté' );
        }
        $this->_date_derniere_refresh = time ();
    }


    public static function connectee ()
    {
        $connectee = FALSE;
        if ( isset( $_SESSION[ 'user' ] ) ) {
            $user = unserialize ( $_SESSION[ 'user' ] );
            if ( $user->connectedOrNot () ) {
                $connectee = TRUE;
            }
        }

        return $connectee;
    }

    static function getJoueur ( $username )
    {
        self::requeteFromDB ( "select id_user from users where username=:username", array ( 'username' => $username ) );
        //TODO
    }

    static function getJoueurLePlusRiche ()
    {
        $resultatPerso = self::requeteFromDB ( "select username,argent from users order by argent DESC LIMIT 1" )[ 0 ];

        return array ( 'username' => $resultatPerso[ 'username' ], 'argent' => $resultatPerso[ 'argent' ] );
    }

    /**
     * Retourne la liste des tuples des personnages triés par level puis par experience
     * @return array
     * @throws Exception
     */
    static function getAllPersoClassement ()
    {
        $donnees = self::requeteFromDB ( "SELECT  users.username, personnage.nom,element.nom as element, personnage.niveau, personnage.experience, personnage.attaques, personnage.hp_max, personnage.mp_max, personnage.puissance, personnage.defense  FROM
  ((users INNER JOIN equipe ON users.id_user = equipe.id_user)
    INNER JOIN personnage ON equipe.id_equipe = personnage.id_equipe) INNER JOIN element ON personnage.element = element.id_element
WHERE 1 = 1 order by personnage.niveau DESC, personnage.experience DESC;" );

        return $donnees;
    }

    /**
     * Retourne la liste des personnages du classement
     * sous la forme : username, niveauTotal, nombrePerso, niveauMax
     * seul les user qui se sont deja connectee sont pris
     *
     * @return array
     * @throws Exception
     */
    static function getAllJoueurClassement ()
    {
        $listes_user = self::requeteFromDB ( "select id_user, username,connected from users where  last_connection is not NULL" );
        $donnees     = array ();
        foreach ( $listes_user as $user ) {
            $joueur                = self::requeteFromDB ( "select SUM(personnage.niveau) as niveauTotal,COUNT(id_personnage) as nombrePerso, MAX(personnage.niveau) as niveauMax from equipe INNER JOIN personnage ON equipe.id_equipe = personnage.id_equipe where equipe.id_user=:id_user;
", array ( 'id_user' => $user[ 'id_user' ] ) )[ 0 ];
            $joueur[ 'username' ]  = $user[ 'username' ];
            $joueur[ 'connected' ] = $user[ 'connected' ];
            array_push ( $donnees, $joueur );
        }

        return $donnees;
    }

    /**
     * Ajoute un personnage au joueur courant
     * Verifie si le personnage n'appartient pas déja au joueur
     * Modifie l'id equipe du personnage pour mettre celui du joueur courant
     *
     * @param $personnage
     *
     * @throws Exception if(Personnage==NULL)
     */
    function addPersonnage ( $personnage )
    {
        if ( $personnage != NULL ) {
            if ( $this->getPersonnageWithID ( $personnage->getIdPersonnage () ) == NULL ) {
                $this->_equipes[ 1 ]->addPersonnage ( $personnage );
            } else {
                throw new Exception( 'Exception, ajout du personnage impossible car personnage appartient deja au joueur' );
            }
        } else {
            throw new Exception( 'Exception, ajout du personnage impossible car personnage null' );
        }
    }

    /**
     * Appel la fonction mère pour l'affichage de la liste des personnages
     * @return mixed|string
     */
    function __toString ()
    {
        return "Identifiant joueur: " . $this->_id_user . parent::__toString ();
    }

    function getParticipant ()
    {
        return array (
                'id_user'  => $this->_id_user,
                'username' => $this->_username,
                'argent'   => $this->_argent,
                'equipes'  => $this->_equipes
        );
    }

    function depenser ( $argent )
    {
        if ( $this->_argent - $argent >= 0 ) {
            $this->_argent = $this->_argent - $argent;
            //ICI on met à jour l'argent du joueur dans la bdd
            $requete = "UPDATE users SET argent = :argent WHERE id_user =:id_user";
            try {
                $reponse = self::$database->prepare ( $requete );
                $reponse->execute (
                        array (
                                'id_user' => $this->_id_user,
                                'argent'  => $this->_argent
                        ) );
            } catch ( PDOException $e ) {
                echo 'Échec lors de la connexion : ' . $e->getMessage ();
            }
        } else {
            throw new Exception( 'Vous ne possedez pas suffisament de gils pour l\'achat' );
        }
    }

    function ajouterArgent ( $argent )
    {
        if ( $argent < 0 ) {
            throw new Exception( 'ajout d\' une somme d\'argent négative' );
        } else {
            $this->_argent = $this->_argent + $argent;
            //ICI on met à jour l'argent du joueur dans la bdd
            $requete = "UPDATE users SET argent = :argent WHERE id_user =:id_user";
            try {
                $reponse = self::$database->prepare ( $requete );
                $reponse->execute (
                        array (
                                'id_user' => $this->_id_user,
                                'argent'  => $this->_argent
                        ) );
            } catch ( PDOException $e ) {
                echo 'Échec lors de la connexion : ' . $e->getMessage ();
            }
        }
    }

    //TODO a mettre à jour
    function ajouterPourcentExperience ( $pourcentXP )
    {
        $this->getEquipeOne ()->ajouterPourcentExperience ( $pourcentXP );
    }

    function attaquerEnnemi ( $participant, $i )
    {
        try {
            $personnage       = $this->getEquipeOne ()->getPersonnages ()[ $i ];
            $personnageTarget = $participant->getEquipeOne ()->getPersonnagePlusFaibleVivant ();
            //ON A RAJOUTER LA NOTION D'ELEMENT
            try {
                $degats = $personnage->attaquer ( 0 );
            } catch ( Exception $e ) {
                $degats = $personnage->attaquer ( 2 );
            }
            // ON mulitplie par le ratio de l'élement
            $degats = $degats * Element::getRatioDegatElement ( $personnage->getElement (), $personnageTarget->getElement () );
            //DEBUG
            //echo '<br/>========= ' . $personnage . ' a fait ' . $degats . ' à lenemi : ' . $personnageTarget . ' ======<br/>';
            $personnageTarget->subirDegats ( $degats );
        } catch ( Exception $e ) {
            print_r ( $e );
        }
        $this->refresh ();
    }

    /**
     * Rafraichit le joueur depuis la bdd
     * Toutes les 2 minutes
     */
    function refresh ()
    {
        $now       = time ();
        $date_diff = $this->dateDiff ( $now, $this->_date_derniere_refresh );
        if ( $date_diff[ 'minute' ] > 2 ) {
            $donneesJoueur = static::requeteFromDB ( "select argent from users where id_user=:id_user", array ( 'id_user' => $this->_id_user ) )[ 0 ];
            $this->_argent = $donneesJoueur[ 'argent' ];
            foreach ( $this->_equipes as $equipe ) {
                $equipe->refresh ();
            }
            $this->_date_derniere_refresh = time ();
        } else {
        }
    }

    function dateDiff ( $date1, $date2 )
    {
        $diff               = abs ( $date1 - $date2 ); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
        $retour             = array ();
        $tmp                = $diff;
        $retour[ 'second' ] = $tmp % 60;
        $tmp                = floor ( ( $tmp - $retour[ 'second' ] ) / 60 );
        $retour[ 'minute' ] = $tmp % 60;
        $tmp                = floor ( ( $tmp - $retour[ 'minute' ] ) / 60 );
        $retour[ 'hour' ]   = $tmp % 24;
        $tmp                = floor ( ( $tmp - $retour[ 'hour' ] ) / 24 );
        $retour[ 'day' ]    = $tmp;

        return $retour;
    }

    function getArgent ()
    {
        return $this->_argent;
    }

    /**
     * Retourne le personnage le plus fort du joueur
     * parmis le stock et parmis l'equipe principale
     * @return Personnage
     */
    function getPersonnagePlusFort ()
    {
        $personnage1 = $this->_equipes[ 0 ]->getPersonnagePlusFort ();
        $personnage2 = $this->_equipes[ 1 ]->getPersonnagePlusFort ();

        return Personnage::getPersonnagePlusHL ( $personnage1, $personnage2 );
    }

    function incrementerVictoire ()
    {
        $this->_nombre_victoire++;
        self::requeteFromDB ( "update users set nombre_victoire=:nombre_victoire where id_user = :id_user", array ( 'id_user' => $this->_id_user, 'nombre_victoire' => $this->_nombre_victoire ) );
    }

    function incrementerDefaite ()
    {
        $this->_nombre_defaite++;
        self::requeteFromDB ( "update users set nombre_defaite=:nombre_defaite where id_user = :id_user", array ( 'id_user' => $this->_id_user, 'nombre_defaite' => $this->_nombre_defaite ) );
    }
}


