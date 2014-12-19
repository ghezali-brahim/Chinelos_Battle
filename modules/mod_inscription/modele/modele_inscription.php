<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

class ModInscriptionModeleInscription extends DBMapper
{

    static function inscription($user)
    {
        //print_r($user);
        if ($user != NULL) {

            try {
                //Verification username deja existant
                $reponse = self::$database->prepare('SELECT id_user FROM users WHERE username = :username');
                $reponse->execute(array('username' => $user[0]));
            } catch (PDOException $e) {
                echo 'Échec lors de la verification du username : ' . $e->getMessage();
            }
            $compteur = $reponse->rowCount();
            if ($compteur != 0) {
                $reussit = 0;
                echo $user[0] . " : username déja existant";
            }
            else {
                $reussit = 1;
                try {
                    $query = "INSERT INTO users VALUES('', ?, ?, ?, 10, 0)";
                    $req   = self::$database->prepare($query);
                    $req->execute($user);
                    $req->fetchAll();
                } catch (PDOException $e) {
                    echo 'Échec lors de la creation de l\'utilisateur : ' . $e->getMessage();
                }
            }

        }
        else {
            $reussit = 0;
        }

        return $reussit;
    }
}

