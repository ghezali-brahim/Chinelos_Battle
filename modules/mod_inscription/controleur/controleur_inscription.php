<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

class ModInscriptionControleurInscription
{

    public function accueilModule()
    {
        require_once MOD_BPATH . DIR_SEP . "vue/vue_inscription.php";
        ModInscriptionVueInscription::affAccueilModule();
    }

    public function inscription()
    {
        require_once MOD_BPATH . DIR_SEP . "modele/modele_inscription.php";
        require_once MOD_BPATH . DIR_SEP . "vue/vue_inscription.php";

        if (isset ($_SESSION ['id_user']) && $_SESSION['id_user'] != NULL) {
            echo 'Vous êtes déja inscrit ';
        }
        else {
            $user = self::verificationContenu();
            //print_r($user);
            $reussit = ModInscriptionModeleInscription::inscription($user);
            ModInscriptionVueInscription::inscription($reussit);
        }
    }

    public function verificationContenu()
    {
        $reussit = 1;
        //Verification existance des variables
        if (isset($_POST ['username'])) {
            $username = htmlspecialchars($_POST ['username']);
        }
        if (isset($_POST ['password'])) {
            $password = htmlspecialchars($_POST ['password']);
        }
        if (isset($_POST ['password2'])) {
            $password2 = htmlspecialchars($_POST ['password2']);
        }
        if (isset($_POST ['email'])) {
            $email = htmlspecialchars($_POST ['email']);
        }

        if (isset ($username) && isset ($password) && isset ($password2) && isset ($email)) {
            if ($username != NULL && $password != NULL && $password2 != NULL && $email != NULL) {
                if (strlen($username) >= 32 && strlen($username) < 5) {
                    $reussit = 0;
                    echo "'$username' incorrecte";
                }
                if (strlen($password) >= 15 && strlen($password) < 5) {
                    $reussit = 0;
                    echo "'$password' mot de passe non compris entre 5 et 14 caracteres";
                }
                if ($password != $password2) {
                    $reussit = 0;
                    echo 'mot de passe différent';
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $reussit = 0;
                    echo "{$email} n'est pas une adresse email valide.";
                }
            }
            else {
                $reussit = 0;
            }
        }
        else {
            $reussit = 0;
        }

        if ($reussit == 1) {
            $password = sha1($password);

            return array(
                $username,
                $password,
                $email
            );
        }
        else {
            return NULL;
        }
    }
}

