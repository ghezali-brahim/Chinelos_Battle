<?php

$host      = "localhost";
$user      = "caporal";
$password  = "caporal";
$dbname    = "test";
$dns       = "mysql:host=$host;dbname=$dbname";
$connexion = new PDO ( $dns, $user, $password );

/*
$host      = "database-etudiants.iut.univ-paris8.fr";
$user      = "dutinfopw201416";
$password  = "esymeryn";
$dbname    = $user;
$dns       = "mysql:host=$host;dbname=$dbname";
$connexion = new PDO ($dns, $user, $password);
*/