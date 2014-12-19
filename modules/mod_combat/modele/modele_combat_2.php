<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    die ( "Vous n'avez pas accès directement à ce fichier" );

//TODO
class Combat extends DBMapper
{
    protected $nbrTour;
    private   $_participant1;
    private   $_participant2;
    private   $tourDe;// indice du participant dont c'est le tour
    private   $indicePersonnagesEquipe1;
    private   $indicePersonnagesEquipe2;

    function __construct ( $participant1, $participant2 )
    {
        $this->_participant1            = $participant1;
        $this->_participant2            = $participant2;
        $this->nbrTour                  = 1;
        $this->tourDe                   = 1;
        $this->indicePersonnagesEquipe1 = 0;
        $this->indicePersonnagesEquipe2 = 0;
    }

    function deroulementTour ()
    {
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

    function __toString ()
    {
        $text = $this->getEquipe1 ()->retourneAffichageEquipe () . "<br/>_________________________________________VS_________________________________________<br/><br/>" . $this->getEquipe2 ()->retourneAffichageEquipe ();
        $text = $text . '<a href="index.php?module=combat&action=passerTour">PasserTour</a>';

        return $text;
    }

    protected function getEquipe1 ()
    {
        return $this->_participant1->getEquipeOne ();
    }

    protected function getEquipe2 ()
    {
        return $this->_participant2->getEquipeOne ();
    }

    protected function getEquipe1Vivant ()
    {
        return $this->getEquipe1 ()->getPersonnageVivant ();
    }

    protected function getEquipe2Vivant ()
    {
        return $this->getEquipe2 ()->getPersonnageVivant ();
    }

    private function recompenser ()
    {
        //TODO
    }
}