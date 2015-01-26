<?php
if ( ! defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );


//TODO
class Combat extends DBMapper {

    protected $id_combat;
    protected $nbrTour;
    private   $_participant1;
    private   $_participant2;
    private   $indice_tour_de_joueur;// indice du participant dont c'est le tour
    private   $indicePersonnagesEquipe1;
    private   $indicePersonnagesEquipe2;
    private   $_joueurReel;
    private   $vainqueur;

    function __construct ( $participant1, $participant2 ) {
        if(is_a($participant2,new Joueur)){
            $this->_joueurReel=TRUE;
        }else{
            $this->_joueurReel=FALSE;
        }
        if($this->_joueurReel){
            $donnees=array('id_joueur_1' => $participant1->getIdUser(), 'id_joueur_2' =>$participant2->getIdUser());
            self::requeteFromDB("INSERT INTO combats(id_joueur_1, id_joueur_2) VALUES(:id_joueur_1, :id_joueur_2)",$donnees);
            print_r($donnees);
        }
        $this->_participant1            = $participant1;
        $this->_participant2            = $participant2;
        $this->nbrTour                  = 0;
        $this->tourDe                   = 1;
        $this->indicePersonnagesEquipe1 = 0;
        $this->indicePersonnagesEquipe2 = 0;
        $this->indice_tour_de_joueur = 0;
    }

    /**
     * @return int
     */
    public function getNbrTour () {
        return $this->nbrTour;
    }

    function demarrerCombat() {
        $donnees         = array ( 'id_joueur_1' => $this->_participant1->getIdUser (), 'id_joueur_2' => $this->_participant2->getIdUser () );
        $this->id_combat = self::requeteFromDB ( "select id_combat from combats where id_joueur_1=:id_joueur_1 AND id_joueur_2=:id_joueur_2 ORDER BY id_combat DESC LIMIT 1", $donnees )[ 0 ];
        $this->nbrTour=1;
        $this->indicePersonnagesEquipe1=0;
        $this->indicePersonnagesEquipe2=0;
        $donnees=array('indice_perso_j1' => $this->indicePersonnagesEquipe1, 'indice_perso_j2' => $this->indicePersonnagesEquipe2, 'nombre_tour' => $this->nbrTour, 'id_combat' => $this->id_combat);
        self::requeteFromDB("UPDATE combats SET indice_perso_j1 = :indice_perso_j1, indice_perso_j2 = :indice_perso_j2, nombre_tour = :nombre_tour WHERE id_combat =:id_combat",$donnees);
    }

    function deroulementTour () {
        if ( $this->tourDe == 1 ) {
            //TODO
            $this->indicePersonnagesEquipe1++;
            $this->tourDe = 2;
        } else {
            //TODO
            $this->indicePersonnagesEquipe2++;
            $this->tourDe = 1;
        }
    }

    function incrementerIndicePerso($participant){
        $participant->incrementerIndicePersoActuelParticipant();
    }

    function __toString () {
        $text = $this->getEquipe1 ()->retourneAffichageEquipe () . "<br/>_________________________________________VS_________________________________________<br/><br/>" . $this->getEquipe2 ()->retourneAffichageEquipe ();
        $text = $text . '<a href="index.php?module=combat&action=passerTour">PasserTour</a>';

        return $text;
    }

    protected function getEquipe1 () {
        return $this->_participant1->getEquipeOne ();
    }

    protected function getEquipe2 () {
        return $this->_participant2->getEquipeOne ();
    }

    private function recompenser () {
        if ( count ( $this->getEquipe1Vivant () ) == 0 ) {
            $gagnant = $this->_participant1;
        } else if ( count ( $this->getEquipe2Vivant () ) == 0 ) {
            $gagnant = $this->_participant2;
        } else {
            throw new Exception( "Impossible de récompenser alors qu'il n'y a pas de gagnant" );
        }
        // si lvl total inférieur à celui de l'enemi alors + de bonus
        if ( $gagnant->getNiveauTotalParticipant () > $this->_participant1->getNiveauTotalParticipant () || $gagnant->getNiveauTotalParticipant () > $this->_participant2->getNiveauTotalParticipant () ) {
            //ICI recompense sous la forme (argent, %xp)
            $recompense_gagnant = array ( 'argent' => 10, 'pourcentXP' => 20 );
        } else {
            $recompense_gagnant = array ( 'argent' => 5, 'pourcentXP' => 15 );
        }
        echo "votre récompense de fin de combat est : " . $recompense_gagnant[ 'argent' ] . "gils ainsi que " . $recompense_gagnant[ 'pourcentXP' ] . "% d'experience. <br/>";
        $gagnant->ajouterArgent ( $recompense_gagnant[ 'argent' ] );
        $gagnant->ajouterPourcentExperience ( $recompense_gagnant[ 'pourcentXP' ] );
    }

    protected function getEquipe1Vivant () {
        return $this->getEquipe1 ()->getPersonnageVivant ();
    }

    protected function getEquipe2Vivant () {
        return $this->getEquipe2 ()->getPersonnageVivant ();
    }
}