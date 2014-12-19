<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "../objects/modele_participant.php";


class Joueur extends Participant
{
    //Player
    protected $_id_user;
    protected $_username;
    protected $_argent;

    /**
     * On créer un Joueur à partir de la base de données; On verifie d'abbord si le joueur est connecté.
     */
    function __construct ()
    {
        if ( self::connectee () ) {
            $requete = "SELECT id_user, username, argent FROM users WHERE username = :username AND id_user = :id_user";
            try {
                $reponse = self::$database->prepare ( $requete );
                $reponse->execute (
                        array (
                                'username' => $_SESSION [ 'username' ],
                                'id_user'  => $_SESSION [ 'id_user' ]
                        ) );
            } catch ( PDOException $e ) {
                echo 'Échec lors de la connexion : ' . $e->getMessage ();
            }
            //Recuperation infos du joueur
            $resultat        = $reponse->fetch ();
            $this->_id_user  = $resultat[ 'id_user' ];
            $this->_username = $resultat[ 'username' ];
            $this->_argent   = $resultat[ 'argent' ];
            // Recuperation de la liste des équipes
            $requete = "SELECT id_equipe FROM equipe WHERE id_user = :id_user";
            try {
                $reponse = self::$database->prepare ( $requete );
                $reponse->execute (
                        array (
                                'id_user' => $_SESSION [ 'id_user' ]
                        ) );
            } catch ( PDOException $e ) {
                echo 'Echec lors de la connexion : ' . $e->getMessage ();
            }
            $listeEquipes   = $reponse->fetchall ();
            $this->_equipes = array ();
            if ( count ( $listeEquipes ) == 0 ) {
                Equipe::createTwoEquipeForBD ( $this->_id_user );
                // Recuperation de la liste des équipes
                $requete = "SELECT id_equipe FROM equipe WHERE id_user = :id_user";
                try {
                    $reponse = self::$database->prepare ( $requete );
                    $reponse->execute (
                            array (
                                    'id_user' => $_SESSION [ 'id_user' ]
                            ) );
                } catch ( PDOException $e ) {
                    echo 'Echec lors de la connexion : ' . $e->getMessage ();
                }
                $listeEquipes   = $reponse->fetchall ();
                $this->_equipes = array ();
            }
            foreach ( $listeEquipes as $id_equipe ) {
                $equipe = Equipe::createEquipeFromBD ( $id_equipe[ 'id_equipe' ] );
                array_push ( $this->_equipes, $equipe );
            }
        } else {
            die( 'vous n\'etes pas connecté' );
        }
    }


    public static function connectee ()
    {
        $connectee = FALSE;
        //Verification de la connexion
        if ( isset( $_SESSION[ 'username' ] ) && isset( $_SESSION[ 'id_user' ] ) ) {
            if ( $_SESSION[ 'username' ] != NULL && $_SESSION[ 'id_user' ] != NULL ) {
                $requete = "SELECT id_user, username FROM users WHERE username = :username AND id_user = :id_user";
                try {
                    $reponse = self::$database->prepare ( $requete );
                    $reponse->execute (
                            array (
                                    'username' => $_SESSION [ 'username' ],
                                    'id_user'  => $_SESSION [ 'id_user' ]
                            ) );
                } catch ( PDOException $e ) {
                    echo 'Échec lors de la connexion : ' . $e->getMessage ();
                }
                // Verifie la validité des valeurs de sessions
                // Si les valeurs de sessions n'existe pas dans la bdd; alors erreur
                if ( $reponse->rowCount () > 0 ) {
                    $connectee = TRUE;
                }
                // FIN verification connexion
            }
        }

        return $connectee;
    }

    /** Ajoute un personnage au joueur courant
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

    /** Rafraichit le joueur depuis la bdd
     *
     */
    function refresh ()
    {
        // TODO: Implement refresh() method.
    }

    /** Appel la fonction mère pour l'affichage de la liste des personnages
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
            echo '<br/>========= ' . $personnage . ' a fait ' . $degats . ' à lenemi : ' . $personnageTarget . ' ======<br/>';
            $personnageTarget->subirDegats ( $degats );
        } catch ( Exception $e ) {
            print_r ( $e );
        }
    }

    function getArgent ()
    {
        return $this->_argent;
    }
}


