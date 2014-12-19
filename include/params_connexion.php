<?php

$host="localhost";
$user="caporal";
$password="caporal";
$dbname="test";
$dns ="mysql:host=$host;dbname=$dbname";
$connexion = new PDO ($dns, $user, $password);
?>
