<?php

$host      = "localhost";
$user      = "root";
$password  = "";
$dbname    = "test";
$dns       = "mysql:host=$host;dbname=$dbname";
$connexion = new PDO ( $dns, $user, $password );

