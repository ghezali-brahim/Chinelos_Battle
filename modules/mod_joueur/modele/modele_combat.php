<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

class Combat extends DBMapper
{

    protected $participant_1;
    protected $participant_2;

    function __construct($participant_1, $participant_2)
    {
        $this->participant_1 = $participant_1;
        $this->participant_2 = $participant_2;
    }

    function afficher()
    {
        ModJoueurVueJoueur::afficherPersonnages($this->participant_1->getPersonnages());
        echo '<br><br>';
        echo '_________________________________________VS_________________________________________';
        echo '<br><br>';
        ModJoueurVueJoueur::afficherPersonnages($this->participant_2->getPersonnages());
    }
}