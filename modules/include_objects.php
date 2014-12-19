<?php


$fichiers = glob("./modules/objects/*", GLOB_NOSORT);
foreach ($fichiers as $fichier) {
    require_once($fichier);
}