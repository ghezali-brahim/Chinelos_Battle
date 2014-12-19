<?php

include("../include/params_connexion.php");
$connexion = new PDO($dns, $user, $password);

include("install_mod_connexion.php");
include("install_mod_joueur.php");


