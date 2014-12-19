<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");
class Personnage extends DBMapper {


protected $id_personnage;
protected $nom;
protected $element;
protected $niveau;
protected $experience;
protected $attaques;
protected $hp;
protected $hp_max;
protected $mp;
protected $mp_max;
protected $puissance;
protected $defense;
protected $id_user;

/**
 *
 * @param $personnageElement
 */
function __construct($personnageElement)
{
    if($personnageElement['id_personnage']>=0){
        $this->id_personnage = $personnageElement['id_personnage'];
    }else{
        throw new Exception('id personnage incorrecte');
    }
    if($personnageElement['nom']!=null){
        $this->nom           = $personnageElement['nom'];
    }else{
        throw new Exception('id personnage incorrecte');
    }
    if($personnageElement['id_personnage']>=0){
        $this->id_personnage = $personnageElement['id_personnage'];
    }else{
        throw new Exception('id personnage incorrecte');
    }
    $this->nom           = $personnageElement['nom'];
    $this->element       = $personnageElement['element'];
    $this->niveau        = $personnageElement['niveau'];
    $this->experience    = $personnageElement['experience'];
    $this->attaques      = $personnageElement['attaques'];
    $this->hp            = $personnageElement['hp'];
    $this->hp_max        = $personnageElement['hp_max'];
    $this->mp            = $personnageElement['mp'];
    $this->mp_max        = $personnageElement['mp_max'];
    $this->puissance     = $personnageElement['puissance'];
    $this->defense       = $personnageElement['defense'];
    if (!isset($personnageElement['id_user'])) {
        $this->id_user = NULL;
    }
    else {
        $this->id_user = $personnageElement['id_user'];
    }
    //TODO récuperer les valeurs de la bdd en fonctions de l'id_equipe

}/**
 * @return mixed
 */
public function getIdPersonnage()
{
    return $this->id_personnage;
}

static public function createPersonnage($niveau)
{
    //ICI on récupère les informations de palier
    $requete = "SELECT DISTINCT experience,hp_max,mp_max,puissance,defense FROM niveau WHERE niveau = :niveau";
    try {
        $reponse = self::$database->prepare($requete);
        $reponse->execute(
            array(
                'niveau' => $niveau
            ));
    } catch (PDOException $e) {
        echo 'Échec lors de la connexion : ' . $e->getMessage();
    }
    $caracNiveau = $reponse->fetch();
    //ICI on récupère l'id personnage max
    $requete = "SELECT max(id_personnage) FROM personnage";
    try {
        $reponse = self::$database->prepare($requete);
        $reponse->execute();
    } catch (PDOException $e) {
        echo 'Échec lors de la connexion : ' . $e->getMessage();
    }
    // Ici on détermine l'identifiant personnage max +1 afin d'en créer un nouveau
    $resultat      = $reponse->fetch();
    $id_personnage = $resultat[0] + 1;

    // On prépare les éléments pour la création du Personnage
    $personnageElement = array(
        'id_personnage' => $id_personnage,
        'nom'           => "Monstre",
        'element'       => 0,
        'niveau'        => $niveau,
        'experience'    => $caracNiveau['experience'],
        'attaques'      => "attaque1; attaque2",
        'hp'            => $caracNiveau['hp_max'],
        'hp_max'        => $caracNiveau['hp_max'],
        'mp'            => $caracNiveau['mp_max'],
        'mp_max'        => $caracNiveau['mp_max'],
        'puissance'     => $caracNiveau['puissance'],
        'defense'       => $caracNiveau['defense']
    );

    //ICI on créer le personnage dans la base de donnée
    $requete = "INSERT INTO personnage VALUES(:id_personnage, :nom, :element, :niveau, :experience, :attaques, :hp, :hp_max, :mp, :mp_max, :puissance, :defense, NULL)";
    try {
        $reponse = self::$database->prepare($requete);
        $reponse->execute($personnageElement);
    } catch (PDOException $e) {
        echo 'Échec lors de la connexion : ' . $e->getMessage();
    }


    // On créer l'objet Personnage
    return new Personnage($personnageElement);
}/**
 * @param mixed $id_user
 */
public function setIdUser($id_user)
{
    //Changement id_user du personnage
    $requete = "UPDATE personnage SET id_user = :id_user WHERE id_personnage= :id_personnage";
    try {
        $reponse = self::$database->prepare($requete);
        $reponse->execute(
            array(
                'id_user'       => $id_user,
                'id_personnage' => $this->id_personnage
            ));
    } catch (PDOException $e) {
        echo 'Échec lors de la connexion : ' . $e->getMessage();
    }
    $this->id_user = $id_user;

}
static public function getPersonnageFromBD($id_personnage)
{

    //ICI on récupère les informations du personnage
    $requete = "SELECT DISTINCT * FROM personnage WHERE id_personnage = :id_personnage";
    try {
        $reponse = self::$database->prepare($requete);
        $reponse->execute(
            array(
                'id_personnage' => $id_personnage
            ));
    } catch (PDOException $e) {
        echo 'Échec lors de la connexion : ' . $e->getMessage();
    }
    $personnageElement = $reponse->fetch();

    return new Personnage($personnageElement);
}
public function getPersonnage()
{
    return array(
        'id_personnage' => $this->id_personnage,
        'nom'           => $this->nom,
        'element'       => $this->element,
        'niveau'        => $this->niveau,
        'experience'    => $this->experience,
        'attaques'      => $this->attaques,
        'hp'            => $this->hp,
        'hp_max'        => $this->hp,
        'mp'            => $this->mp,
        'mp_max'        => $this->mp,
        'puissance'     => $this->puissance,
        'defense'       => $this->defense,
        'id_user'       => $this->id_user
    );
}
public function afficher() {
?>
<table border="1">
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
        <th> ID User</th>
    </tr>
    <?php

    echo "<tr>\n";
    echo "<td>" . $this->id_personnage . "</td>\n";
    echo "<td>" . $this->nom . "</td>\n";
    echo "<td>" . $this->element . "</td>\n";
    echo "<td>" . $this->niveau . "</td>\n";
    echo "<td>" . $this->experience . "</td>\n";
    echo "<td>" . $this->attaques . "</td>\n";
    echo "<td>" . $this->hp . "</td>\n";
    echo "<td>" . $this->mp . "</td>\n";
    echo "<td>" . $this->puissance . "</td>\n";
    echo "<td>" . $this->defense . "</td>\n";
    echo "<td>" . $this->id_user . "</td>\n";
    echo "</tr>";
    echo "</table>";
    }

    }
    ?>
