<?php
// Configuration - utilise les variables d'environnement Docker ou les valeurs par défaut
define('DB_HOST', getenv('DB_HOST') ?: '172.16.7.131');
define('DB_NAME', getenv('DB_NAME') ?: 'db_s2_ETU003901');
define('DB_USER', getenv('DB_USER') ?: 'ETU003901');
define('DB_PASS', getenv('DB_PASS') ?: 'rqNtrJ3t');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// Base URL pour le déploiement
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
