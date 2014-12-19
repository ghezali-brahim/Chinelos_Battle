<?php

$reqTableUsers = "CREATE TABLE IF NOT EXISTS users (
id_user SERIAL,
username VARCHAR( 255 ) NOT NULL UNIQUE,
password VARCHAR( 255 ) NOT NULL ,
email VARCHAR( 255 ) NOT NULL UNIQUE,
argent INT
) ENGINE=MYISAM DEFAULT CHARACTER SET=utf8;";

$connexion->query($reqTableUsers);

/*
$reqAlter = "ALTER TABLE `mod_connexion_users` ADD INDEX(`login`)";
$connexion->query($reqAlter);
*/
