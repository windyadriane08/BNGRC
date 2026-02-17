<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/config.php';
require_once __DIR__.'/routes.php';

try {
    // Connexion via host (serveur distant) au lieu de socket
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erreur connexion DB : " . $e->getMessage());
}

// Enregistrer db() comme méthode Flight
Flight::register('db', 'PDO', [$dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]]);
Flight::set('flight.views.path', __DIR__.'/views');
Flight::set('flight.base_url', BASE_URL);