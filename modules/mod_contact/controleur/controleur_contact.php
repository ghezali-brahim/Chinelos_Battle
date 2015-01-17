<?php
if ( !defined ( 'TEST_INCLUDE' ) )
    exit ( "Vous n'avez pas accès directement à ce fichier" );
require_once MOD_BPATH . DIR_SEP . "modele/modele_contact.php";
require_once MOD_BPATH . DIR_SEP . "vue/vue_contact.php";


class ModContactControleurContact
{

    public function accueilContact ()
    {
        ModContactVueContact::afficherAccueilContact ();
    }

    public function envoyerMail ()
    {
        if ( isset( $_POST[ 'objet' ] ) && isset( $_POST[ 'message' ] ) ) {
            $donnees = Array ( 'objet'   => $_POST[ 'objet' ],
                               'message' => $_POST[ 'message' ] );
            if ( is_string ( $donnees[ 'objet' ] ) && is_string ( $donnees[ 'message' ] ) ) {
                $contact = new ModContactModeleContact();
                $contact->envoyerMail ( $donnees );
                mail ( 'jojax77@hotmail.fr', 'HI', 'Marche' );
                echo "Mail envoyé !";
            } else {
                echo "Echec de l'envoi du mail !";
            }
        }
    }
}
