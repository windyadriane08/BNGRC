<?php
/**
 * Point d'entrée pour déploiement sur serveur distant
 * Ce fichier est à la racine du dossier bngrc_etu4038_etu3901
 */
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

Flight::start();
