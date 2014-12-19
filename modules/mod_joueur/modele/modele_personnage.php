<?php
if (!defined('TEST_INCLUDE'))
    die ("Vous n'avez pas accès directement à ce fichier");

class Personnage extends DBMapper
{

    static    $CARAC_ADD_FOR_UPPING = array(
        'hp_max'    => 5,
        'mp_max'    => 5,
        'puissance' => 5,
        'defense'   => 5
    );
    static    $id_personnage_serial = 9000;// IL ne faut pas dépasser 9000 persos
    protected $_id_personnage;
    protected $_nom;
    protected $_element;//TODO créer des objets de type élément, pour le moment élément est un int
    protected $_niveau;//TODO créer un objet de type niveau qui stoquera l'experience ainsi que le niveau, etc.
    protected $_experience;//TODO voir niveau
    protected $_attaques;
    protected $_hp;
    protected $_hp_max;
    protected $_mp;
    protected $_mp_max;
    protected $_puissance;
    protected $_defense;
    //protected $_id_user;
    protected $_id_equipe;

    /**
     * Constructeur ; vérifie si $personnageElement est correcte
     *
     * @param $personnageElement
     */
    protected function __construct($personnageElement)//TODO Modifier le fonctionnement du constructeur
    {
        if ($personnageElement['id_personnage'] >= 0) {
            $this->_id_personnage = $personnageElement['id_personnage'];
        }
        else {
            throw new Exception('id personnage incorrecte');
        }
        if ($personnageElement['nom'] != NULL) {
            $this->_nom = $personnageElement['nom'];
        }
        else {
            throw new Exception('nom incorrecte');
        }
        if ($personnageElement['element'] >= 0) {
            $this->_element = $personnageElement['element'];
        }
        else {
            throw new Exception('element incorrecte');
        }
        if ($personnageElement['niveau'] >= 0) {
            $this->_niveau = $personnageElement['niveau'];
        }
        else {
            throw new Exception('Niveau incorrecte');
        }
        if ($personnageElement['experience'] >= 0) {
            $this->_experience = $personnageElement['experience'];
        }
        else {
            throw new Exception('Experience incorrecte');
        }

        if ($personnageElement['attaques'] != NULL) {
            $listesIdAttaques              = explode(";", $personnageElement['attaques']);
            $personnageElement['attaques'] = array();
            foreach ($listesIdAttaques as $id_attaque) {
                array_push($personnageElement['attaques'], new Attaque($id_attaque));
            }
            $this->_attaques = $personnageElement['attaques'];
        }
        else {
            throw new Exception('attaques incorrecte');
        }
        if ($personnageElement['hp'] >= 0) {
            $this->_hp = $personnageElement['hp'];
        }
        else {
            throw new Exception('hp incorrecte');
        }
        if ($personnageElement['hp_max'] >= 0) {
            $this->_hp_max = $personnageElement['hp_max'];
        }
        else {
            throw new Exception('hp_max incorrecte');
        }
        if ($personnageElement['mp'] >= 0) {
            $this->_mp = $personnageElement['mp'];
        }
        else {
            throw new Exception('mp incorrecte');
        }
        if ($personnageElement['mp_max'] >= 0) {
            $this->_mp_max = $personnageElement['mp_max'];
        }
        else {
            throw new Exception('mp_max incorrecte');
        }
        if ($personnageElement['puissance'] >= 0) {
            $this->_puissance = $personnageElement['puissance'];
        }
        else {
            throw new Exception('puissance incorrecte');
        }
        if ($personnageElement['defense'] >= 0) {
            $this->_defense = $personnageElement['defense'];
        }
        else {
            throw new Exception('defense incorrecte');
        }

        if (!isset($personnageElement['id_equipe'])) {
            $this->_id_equipe = NULL;
        }
        else {
            $this->_id_equipe = $personnageElement['id_equipe'];
        }

    }

    static public function createPersonnageForBD($niveau)
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

        // Creation de la liste des attaques ($listeAttaques)
        $listeAttaques = array();
        array_push($listeAttaques, new Attaque(1));
        array_push($listeAttaques, new Attaque(2));
        array_push($listeAttaques, new Attaque(3));

        // Creation d'une liste des id des attaques ($listeAttaquesString)
        $listeAttaquesString = "";
        $i                   = 0;
        foreach ($listeAttaques as $attaques) {
            if ($i == 0) {
                $listeAttaquesString = $attaques->getIdAttaque();
            }
            else {
                $listeAttaquesString = $listeAttaquesString . ";" . $attaques->getIdAttaque();
            }
            $i++;
        }
        // On prépare les éléments pour la création du Personnage
        $personnageElement = array(
            'id_personnage' => $id_personnage,
            'nom'           => "Monstre",
            'element'       => 0,
            'niveau'        => $niveau,
            'experience'    => $caracNiveau['experience'],
            'attaques'      => $listeAttaquesString,
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
        // Creation de la liste des attaques ($listeAttaques)
        $listeAttaques = array();
        array_push($listeAttaques, new Attaque(1));
        array_push($listeAttaques, new Attaque(2));
        array_push($listeAttaques, new Attaque(3));

        // Creation d'une liste des id des attaques ($listeAttaquesString)
        $listeAttaquesString = "";
        $i                   = 0;
        foreach ($listeAttaques as $attaques) {
            if ($i == 0) {
                $listeAttaquesString = $attaques->getIdAttaque();
            }
            else {
                $listeAttaquesString = $listeAttaquesString . ";" . $attaques->getIdAttaque();
            }
            $i++;
        }
        self::$id_personnage_serial++;
        // On prépare les éléments pour la création du Personnage
        $personnageElement = array(
            'id_personnage' => self::$id_personnage_serial,
            'nom'           => "Monstre",
            'element'       => 0,
            'niveau'        => $niveau,
            'experience'    => $caracNiveau['experience'],
            'attaques'      => $listeAttaquesString,
            'hp'            => $caracNiveau['hp_max'],
            'hp_max'        => $caracNiveau['hp_max'],
            'mp'            => $caracNiveau['mp_max'],
            'mp_max'        => $caracNiveau['mp_max'],
            'puissance'     => $caracNiveau['puissance'],
            'defense'       => $caracNiveau['defense']
        );

        // On créer l'objet Personnage
        return new Personnage($personnageElement);

    }

    /** Recupere en fonction de l'id personnage le Personnage correspondant
     *
     * @param $id_personnage
     *
     * @return Personnage
     */
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

    /** Ajoute l'experience passé en paramètre au personnage, met à jour le niveau puis met à jours la base de données
     *
     * @param $experience
     *
     * @throws Exception
     */
    public function addExperience($experience)
    {
        if ($experience >= 0) {
            $this->_experience += $experience;
            // Si experience requise pour up, alors up
            if (Niveau::getNiveau($this->_experience) != $this->_niveau) {
                $this->upNiveau();
            }
            //ICI on met à jour l'experience du personnage dans la bdd
            $requete = "UPDATE personnage SET experience = :experience WHERE id_personnage =:id_personnage";
            try {
                $reponse = self::$database->prepare($requete);
                $reponse->execute(
                    array(
                        'id_personnage' => $this->_id_personnage,
                        'experience'    => $this->_experience
                    ));
            } catch (PDOException $e) {
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }
        }
        else {
            throw new Exception('experience ajouté négative impossible');
        }
    }

    public function upNiveau()
    {
        if (Niveau::getNiveau($this->_experience != $this->_niveau)) {
            $this->_niveau = Niveau::getNiveau($this->_experience);
            //On rajoute les caracs
            $this->addPuissance(Personnage::$CARAC_ADD_FOR_UPPING['puissance']);
            $this->addDefense(Personnage::$CARAC_ADD_FOR_UPPING['defense']);
            $this->addHpMax(Personnage::$CARAC_ADD_FOR_UPPING['hp_max']);
            $this->addMpMax(Personnage::$CARAC_ADD_FOR_UPPING['mp_max']);
            //ICI on met à jour le niveau du personnage dans la bdd
            $requete = "UPDATE personnage SET niveau = :niveau WHERE id_personnage =:id_personnage";
            try {
                $reponse = self::$database->prepare($requete);
                $reponse->execute(
                    array(
                        'id_personnage' => $this->_id_personnage,
                        'niveau'        => $this->_niveau
                    ));
            } catch (PDOException $e) {
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }
        }
        else {
            throw new Exception('upNiveau() a été appellé alors qu\'il ne devrais pas y avoir de up');
        }

    }

    public function addPuissance($puissance)
    {
        if ($puissance > 0) {
            $this->_puissance += $puissance;

            $requete = "UPDATE personnage SET puissance = :puissance WHERE id_personnage =:id_personnage";
            try {
                $reponse = self::$database->prepare($requete);
                $reponse->execute(
                    array(
                        'id_personnage' => $this->_id_personnage,
                        'puissance'     => $this->_puissance
                    ));
            } catch (PDOException $e) {
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }
        }
        else {
            throw new Exception('puissance à rajouté négative');
        }
    }

    public function addDefense($defense)
    {
        if ($defense > 0) {
            $this->_defense += $defense;

            $requete = "UPDATE personnage SET defense = :defense WHERE id_personnage =:id_personnage";
            try {
                $reponse = self::$database->prepare($requete);
                $reponse->execute(
                    array(
                        'id_personnage' => $this->_id_personnage,
                        'defense'       => $this->_defense
                    ));
            } catch (PDOException $e) {
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }
        }
        else {
            throw new Exception('defense à rajouté négative');
        }
    }

    public function addHpMax($hp_max)
    {
        if ($hp_max > 0) {
            $this->_hp_max += $hp_max;

            $requete = "UPDATE personnage SET hp_max = :hp_max WHERE id_personnage =:id_personnage";
            try {
                $reponse = self::$database->prepare($requete);
                $reponse->execute(
                    array(
                        'id_personnage' => $this->_id_personnage,
                        'hp_max'        => $this->_hp_max
                    ));
            } catch (PDOException $e) {
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }
        }
        else {
            throw new Exception('hp_max à rajouté négative');
        }
    }

    public function addMpMax($mp_max)
    {
        if ($mp_max > 0) {
            $this->_mp_max += $mp_max;

            $requete = "UPDATE personnage SET mp_max = :mp_max WHERE id_personnage =:id_personnage";
            try {
                $reponse = self::$database->prepare($requete);
                $reponse->execute(
                    array(
                        'id_personnage' => $this->_id_personnage,
                        'mp_max'        => $this->_mp_max
                    ));
            } catch (PDOException $e) {
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }
        }
        else {
            throw new Exception('mp_max à rajouté négative');
        }
    }

    /** On inflige les dégats au personnage:
     * le personnage réduit les dégats de manière
     * aléatoire entre 0 et $this->defense
     *
     * @param $degats
     *
     * @throws Exception
     */
    public function subirDegats($degats)
    {
        if ($degats >= 0) {
            $degats -= rand(0, $this->_defense);
            if ($this->_hp -= $degats < 0) {
                $this->_hp = 0;
            }
            else {
                $this->_hp -= $degats;
            }

            echo '<br>Ouille; degats infligés: ' . $degats;
        }
        else {
            throw new Exception('degats infligé négatif');
        }
    }

    public function attaquer($attaqueIndice)
    {
        $attaque = $this->_attaques[ $attaqueIndice ];
        if (($this->_mp - $attaque->getPmUsed()) < 0) {
            throw new Exception('Nombre de pm non sufisant: ' . $this->_mp . 'PM restant / ' . $attaque->getPmUsed() . 'Pm Necessaire');
        }
        else {
            $this->_mp -= $attaque->getPmUsed();;
            $degatsInfliges = ($this->_puissance) * ($attaque->getDegats());
        }

        return $degatsInfliges;
    }

    /**
     * @return array
     */
    public function getAttaques()
    {
        return $this->_attaques;
    }

    /** retourne l'experience manquant pour monter le niveau suivant
     *
     *
     * @return mixed
     */
    public function getXpManquantPourUp()
    {
        return Niveau::getXpManquantPourUp($this->_experience);
    }

    /** De 0 à 99% xp réalisé
     * @return mixed
     */
    public function getPourcentXp()
    {
        return Niveau::getPourcentXp($this->_experience);
    }

    /**
     * @return mixed
     */
    public function getNiveau()
    {
        return $this->_niveau;
    }

    /**
     *
     */
    public function refresh()
    {
        //TODO
    }

    /**
     * @return mixed
     */
    public function getIdPersonnage()
    {
        return $this->_id_personnage;
    }

    public function isDead()
    {
        return $this->_hp == 0;
    }

    public function infligerDegats($degats)
    {
        if ($this->_hp < 0) {
            $this->_hp = 0;
        }
        else {
            $this->_hp = $this->_hp - $degats;
        }
    }

    /**
     * @param mixed $id_user
     */
    /*
    public function setIdUser($id_user)
    {
        //Changement id_user du personnage
        $requete = "UPDATE personnage SET id_user = :id_user WHERE id_personnage= :id_personnage";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute(
                array(
                    'id_user'       => $id_user,
                    'id_personnage' => $this->_id_personnage
                ));
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }
        $this->_id_user = $id_user;

    }
*/
    /**
     * @param mixed $id_equipe
     */
    public function setIDEquipe($id_equipe = NULL)
    {
        //Changement id_equipe du personnage
        $requete = "UPDATE personnage SET id_equipe = :id_equipe WHERE id_personnage= :id_personnage";
        try {
            $reponse = self::$database->prepare($requete);
            $reponse->execute(
                array(
                    'id_equipe'     => $id_equipe,
                    'id_personnage' => $this->_id_personnage
                ));
        } catch (PDOException $e) {
            echo 'Échec lors de la connexion : ' . $e->getMessage();
        }
        $this->_id_equipe = $id_equipe;


    }

    /** Retourne les éléments du personnage
     * @return array
     */
    public function getPersonnage()
    {
        return array(
            'id_personnage' => $this->_id_personnage,
            'nom'           => $this->_nom,
            'element'       => $this->_element,
            'niveau'        => $this->_niveau,
            'experience'    => $this->_experience,
            'attaques'      => $this->_attaques,
            'hp'            => $this->_hp,
            'hp_max'        => $this->_hp,
            'mp'            => $this->_mp,
            'mp_max'        => $this->_mp,
            'puissance'     => $this->_puissance,
            'defense'       => $this->_defense,
            'id_equipe'     => $this->_id_equipe
        );
    }

    public function afficher()
    {
        echo '<table border="1">
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
        <th> ID Equipe</th>
    </tr>';


        echo "<tr>\n";
        echo "<td>" . $this->_id_personnage . "</td>\n";
        echo "<td>" . $this->_nom . "</td>\n";
        echo "<td>" . $this->_element . "</td>\n";
        echo "<td>" . $this->_niveau . "</td>\n";
        echo "<td>" . $this->_experience . "</td>\n";
        echo "<td>" . $this->getIdAttaques() . "</td>\n";
        echo "<td>" . $this->_hp . "</td>\n";
        echo "<td>" . $this->_mp . "</td>\n";
        echo "<td>" . $this->_puissance . "</td>\n";
        echo "<td>" . $this->_defense . "</td>\n";
        echo "<td>" . $this->_id_equipe . "</td>\n";
        echo "</tr>";
        echo "</table>";
    }

	//RAJOUTER LE 
    public function getIdEquipes(){

		return $this->_id_equipe;
	}


    public function getIdAttaques()
    {
        // Creation d'une liste des id des attaques ($listeAttaquesString)
        $listeAttaquesString = "";
        $i                   = 0;
        foreach ($this->_attaques as $attaques) {
            if ($i == 0) {
                $listeAttaquesString = $attaques->getIdAttaque();
            }
            else {
                $listeAttaquesString = $listeAttaquesString . ";" . $attaques->getIdAttaque();
            }
            $i++;
        }

        return $listeAttaquesString;
    }

}
