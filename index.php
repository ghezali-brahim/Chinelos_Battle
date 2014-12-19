<?php
define ( 'TEST_INCLUDE', 1 );
define ( 'SITE_BPATH', "/projects/TP3/" );
session_start ();
include "include/params_site.php";
include "include" . DIR_SEP . "init_errors.php";
include "include" . DIR_SEP . "params_connexion.php";
include "include" . DIR_SEP . "dbMapper.php";
include "include" . DIR_SEP . "header.php";
$connexion = new PDO( $dns, $user, $password );
DBMapper::init ( $connexion );
$module = isset( $_GET[ 'module' ] ) ? $_GET[ 'module' ] : "connexion";// vin
//Sécurisation de l'include
$module = preg_replace ( "#\W#", "", $module );
include ( "modules" . DIR_SEP . "mod_$module" . DIR_SEP . "mod_$module.php" );
//require 'lib/Logger.class.php';
/*
if( isset($_GET['run']) ){
    header('Content-Type: text/plain; charset=utf-8');

    if( version_compare(PHP_VERSION, '5', '<') ){
        echo "Votre version de PHP (".PHP_VERSION.") est trop ancienne. PHP 5 minimum requis.";
        die();
    }

    // On créé un objet Logger (instanciation)
    $logger = new Logger('./logs');

    // Quelques logs avec différents types, nom et granularité
    $logger->log('erreurs', 'err_php', "Mon message d'erreur", Logger::GRAN_MONTH);
    $logger->log('statistiques', 'clic_liens_externes', "cible du lien : http://www.finalclap.com/", Logger::GRAN_MONTH);
    $logger->log('', 'sans_type', "Ce log n'a pas de type ni de granularité, il est simplement enregistré à la racine du dépôt", Logger::GRAN_VOID);

    // Un log qui enregistre pleins d'information que le visiteur :
    $referer	= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'none';
    $user_agent	= $_SERVER['HTTP_USER_AGENT'];
    $ip			= $_SERVER['REMOTE_ADDR'];
    $port		= $_SERVER['REMOTE_PORT'];
    $uri		= $_SERVER['REQUEST_URI'];
    $method		= $_SERVER['REQUEST_METHOD'];
    $row = "ip: $ip (port $port)	uri: $method $uri	referer: $referer	agent: $user_agent";
    $logger->log('statistiques', 'maxi_info', $row, Logger::GRAN_MONTH);

    echo "Test terminé, vous pouvez maintenant jeter un oeil dans votre dossier logs pour voir ce que le Logger y a écrit...";
    die();
}
*/
include "include" . DIR_SEP . "footer.php";

