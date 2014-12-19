<?php

$host      = "database-etudiants.iut.univ-paris8.fr";
$user      = "dutinfopw20147";
$password  = "udebadyv";
$dbname    = $user;
$dns       = "mysql:host=$host;dbname=$dbname";
$connexion = new PDO ($dns, $user, $password);

