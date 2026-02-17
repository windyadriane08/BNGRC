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
            Flight::redirect(BASE_URL . '/dispatch?error=' . urlencode($e->getMessage()));
        }
    }

    public function validate($mode = 1) {
        try {
            $mode = (int)$mode;
            if ($mode === 3) {
                $resultats = $this->dispatchService->dispatcherDonsMode3();
                $modeName = 'Mode 3 (Proportionnel)';
                // Calculer le reste total
                $reste_total = 0;
                foreach ($resultats as $type_result) {
                    if (isset($type_result['reste'])) {
                        $reste_total += $type_result['reste'];
                    }
                }
                if ($reste_total > 0) {
                    Flight::redirect('/dispatch?success=Dispatch ' . $modeName . ' validé avec succès&reste=' . $reste_total);
                    return;
                }
            } elseif ($mode === 2) {
                $resultats = $this->dispatchService->dispatcherDonsMode2();
                $modeName = 'Mode 2 (Plus petits besoins)';
            } else {
                $resultats = $this->dispatchService->dispatcherDons();
                $modeName = 'Mode 1 (FIFO)';
            }
            Flight::redirect('/dispatch?success=Dispatch ' . $modeName . ' validé avec succès');
        } catch (Exception $e) {
            Flight::redirect('/dispatch?error=' . urlencode($e->getMessage()));
        }
    }

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
