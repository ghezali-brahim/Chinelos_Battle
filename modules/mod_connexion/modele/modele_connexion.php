<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

class ModConnexionModeleConnexion extends DBMapper
{

    public static function connexion()
    {
        $id_user = NULL;
        $requete = "SELECT id_user, password FROM users WHERE username = :username";
        if (isset ($_POST ['username']) && isset ($_POST ['password'])) {
            try {
                $reponse = self::$database->prepare($requete);
                $reponse->execute(
                    array(
                        'username' => $_POST ['username']
                    ));
                $username_trouve = $reponse->rowCount();
            } catch (PDOException $e) {
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }
            //print_r($reponse);
            if ($username_trouve == 1) {
                $resultat = $reponse->fetch();
                if ($resultat ['password'] == sha1($_POST ['password'])) {
                    $id_user               = $resultat ['id_user'];
                    $_SESSION ['id_user']  = $id_user;
                    $_SESSION ['username'] = $_POST ['username'];
                }
                else {
                    echo 'mot de passe incorrecte';
                }
            }
            else {
                echo 'username incorrecte';
            }
        }

        return $id_user;
    }

    public static function deconnexion()
    {
        session_destroy();
    }
}

