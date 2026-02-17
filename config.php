<?php
// Configuration - utilise les variables d'environnement Docker ou les valeurs par défaut
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'bngrc');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// Base URL pour le déploiement
define('BASE_URL', getenv('BASE_URL') ?: '');