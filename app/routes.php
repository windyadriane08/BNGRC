<?php

// Chargement des contrôleurs
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/VilleController.php';
require_once __DIR__ . '/controllers/BesoinController.php';
require_once __DIR__ . '/controllers/DonController.php';
require_once __DIR__ . '/controllers/DispatchController.php';
require_once __DIR__ . '/controllers/AchatController.php';
require_once __DIR__ . '/controllers/RecapController.php';

// Chargement des services
require_once __DIR__ . '/services/DashboardService.php';
require_once __DIR__ . '/services/BesoinService.php';
require_once __DIR__ . '/services/DonService.php';
require_once __DIR__ . '/services/DispatchService.php';
require_once __DIR__ . '/services/Validator.php';

// Chargement des repositories
require_once __DIR__ . '/repositories/VilleRepository.php';
require_once __DIR__ . '/repositories/BesoinRepository.php';
require_once __DIR__ . '/repositories/DonRepository.php';
require_once __DIR__ . '/repositories/AttributionRepository.php';
require_once __DIR__ . '/repositories/AchatRepository.php';
require_once __DIR__ . '/repositories/ConfigRepository.php';

// Page d'accueil - Dashboard
Flight::route('GET /', function() {
    $controller = new DashboardController();
    $controller->index();
});
Flight::route('GET /dashboard', function() {
    $controller = new DashboardController();
    $controller->index();
});

// Gestion des villes
Flight::route('GET /villes', function() {
    $controller = new VilleController();
    $controller->list();
});
Flight::route('GET /villes/create', function() {
    $controller = new VilleController();
    $controller->create();
});
Flight::route('POST /villes/store', function() {
    $controller = new VilleController();
    $controller->store();
});
Flight::route('POST /villes/delete/@id', function($id) {
    $controller = new VilleController();
    $controller->delete($id);
});

// Gestion des besoins
Flight::route('GET /besoins', function() {
    $controller = new BesoinController();
    $controller->list();
});
Flight::route('GET /besoins/ville/@ville_id', function($ville_id) {
    $controller = new BesoinController();
    $controller->list($ville_id);
});
Flight::route('GET /besoins/create', function() {
    $controller = new BesoinController();
    $controller->create();
});
Flight::route('POST /besoins/store', function() {
    $controller = new BesoinController();
    $controller->store();
});
Flight::route('POST /besoins/delete/@id', function($id) {
    $controller = new BesoinController();
    $controller->delete($id);
});

// Gestion des dons
Flight::route('GET /dons', function() {
    $controller = new DonController();
    $controller->list();
});
Flight::route('GET /dons/create', function() {
    $controller = new DonController();
    $controller->create();
});
Flight::route('POST /dons/store', function() {
    $controller = new DonController();
    $controller->store();
});
Flight::route('POST /dons/delete/@id', function($id) {
    $controller = new DonController();
    $controller->delete($id);
});

// Dispatch (simulation et validation)
Flight::route('GET /dispatch', function() {
    $controller = new DispatchController();
    $controller->index();
});
Flight::route('POST /dispatch/simulate/@mode', function($mode) {
    $controller = new DispatchController();
    $controller->simulate($mode);
});
Flight::route('POST /dispatch/simulate', function() {
    $controller = new DispatchController();
    $controller->simulate(1);
});
Flight::route('POST /dispatch/validate/@mode', function($mode) {
    $controller = new DispatchController();
    $controller->validate($mode);
});
Flight::route('POST /dispatch/validate', function() {
    $controller = new DispatchController();
    $controller->validate(1);
});
Flight::route('POST /dispatch/reset', function() {
    $controller = new DispatchController();
    $controller->reset();
});

// Gestion des achats
Flight::route('GET /achats', function() {
    $controller = new AchatController();
    $controller->list();
});
Flight::route('GET /achats/create/@id_besoin', function($id_besoin) {
    $controller = new AchatController();
    $controller->create($id_besoin);
});
Flight::route('POST /achats/store', function() {
    $controller = new AchatController();
    $controller->store();
});
Flight::route('GET /achats/config', function() {
    $controller = new AchatController();
    $controller->config();
});
Flight::route('POST /achats/config', function() {
    $controller = new AchatController();
    $controller->saveConfig();
});

// Page de récapitulation
Flight::route('GET /recap', function() {
    $controller = new RecapController();
    $controller->index();
});
Flight::route('GET /recap/data', function() {
    $controller = new RecapController();
    $controller->data();
});

