<?php

class DispatchController {
    private $dispatchService;
    
    public function __construct() {
        $this->dispatchService = new DispatchService();
    }
    
    public function index() {
        $data = $this->dispatchService->getResultatDispatch();
        
        // Récupérer les messages de succès/erreur
        $data['success'] = Flight::request()->query->success ?? null;
        $data['error'] = Flight::request()->query->error ?? null;
        $data['simulation'] = null;
        
        Flight::render('dispatch/index', $data);
    }

    /**
     * Simule le dispatch sans l'exécuter
     * Affiche un aperçu des attributions qui seraient faites
     */
    public function simulate() {
        try {
            $simulation = $this->dispatchService->simulerDispatch();
            $data = $this->dispatchService->getResultatDispatch();
            $data['simulation'] = $simulation;
            $data['success'] = null;
            $data['error'] = null;
            
            Flight::render('dispatch/index', $data);
        } catch (Exception $e) {
            Flight::redirect('/dispatch?error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Valide et exécute le dispatch
     */
    public function validate() {
        try {
            $resultats = $this->dispatchService->dispatcherDons();
            Flight::redirect('/dispatch?success=Dispatch validé avec succès : ' . count($resultats) . ' attribution(s) créée(s)');
        } catch (Exception $e) {
            Flight::redirect('/dispatch?error=' . urlencode($e->getMessage()));
        }
    }
}
