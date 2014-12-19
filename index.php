<?php
define ('TEST_INCLUDE', 1);
define ('SITE_BPATH', "/projects/TP3/");
session_start();

include "include/params_site.php";
include "include" . DIR_SEP . "init_errors.php";
include "include" . DIR_SEP . "params_connexion.php";
include "include" . DIR_SEP . "dbMapper.php";

include "include" . DIR_SEP . "header.php";

$connexion = new PDO($dns, $user, $password);
DBMapper::init($connexion);


$module = isset($_GET['module']) ? $_GET['module'] : "connexion";// vin

//Sécurisation de l'include
$module = preg_replace("#\W#", "", $module);
include("modules" . DIR_SEP . "mod_$module" . DIR_SEP . "mod_$module.php");


include "include" . DIR_SEP . "footer.php";

