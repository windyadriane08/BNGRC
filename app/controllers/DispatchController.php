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
    public function simulate($mode = 1) {
        try {
            $mode = (int)$mode;
            if ($mode === 3) {
                $simulation = $this->dispatchService->simulerDispatchMode3();
            } elseif ($mode === 2) {
                $simulation = $this->dispatchService->simulerDispatchMode2();
            } else {
                $simulation = $this->dispatchService->simulerDispatch();
                $simulation['mode'] = 1;
                $simulation['mode_nom'] = 'FIFO';
            }
            $data = $this->dispatchService->getResultatDispatch();
            $data['simulation'] = $simulation;
            $data['current_mode'] = $mode;
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
    public function validate($mode = 1) {
        try {
            $mode = (int)$mode;
            if ($mode === 3) {
                $resultats = $this->dispatchService->dispatcherDonsMode3();
                // Calculer le reste total
                $reste_total = 0;
                foreach ($resultats as $type_result) {
                    if (isset($type_result['reste'])) {
                        $reste_total += $type_result['reste'];
                    }
                }
                Flight::redirect('/dispatch?mode=3' . ($reste_total > 0 ? '&reste=' . $reste_total : ''));
                return;
            } elseif ($mode === 2) {
                $resultats = $this->dispatchService->dispatcherDonsMode2();
                Flight::redirect('/dispatch?mode=2');
            } else {
                $resultats = $this->dispatchService->dispatcherDons();
                Flight::redirect('/dispatch?mode=1');
            }
        } catch (Exception $e) {
            Flight::redirect('/dispatch?error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Réinitialise toutes les attributions et achats
     */
    public function reset() {
        try {
            $attributionRepo = new AttributionRepository();
            $attributionRepo->resetAll();
            Flight::redirect('/dispatch?success=Réinitialisation effectuée avec succès. Toutes les attributions et achats ont été supprimés.');
        } catch (Exception $e) {
            Flight::redirect('/dispatch?error=' . urlencode($e->getMessage()));
        }
    }
}