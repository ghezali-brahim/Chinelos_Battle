<?php

include ("../include/params_connexion.php");
$connexion = new PDO($dns, $user, $password);

include ("install_mod_vin.php");
include ("install_mod_connexion.php");
?>

