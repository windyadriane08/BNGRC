<?php

class DonController {
    public function list() {
        $donRepo = new DonRepository();
        $dons = $donRepo->getAll();

        Flight::render('dons/list', ['dons' => $dons]);
    }

    public function create() {
        $besoinRepo = new BesoinRepository();
        $types = $besoinRepo->getTypeRessources();
        
        Flight::render('dons/create', ['types' => $types]);
    }

    public function store() {
        $type_id = Flight::request()->data->type_id;
        $montant = Flight::request()->data->montant; // pour argent
        $quantite = Flight::request()->data->quantite;

        try {
            $donRepo = new DonRepository();
            $besoinRepo = new BesoinRepository();
            $type = $besoinRepo->getTypeById($type_id);
            if ($type && $type['categorie'] === 'argent') {
                $quantite = (float)$montant;
            }
            $donRepo->create($type_id, $quantite);
            Flight::redirect('/dons?success=Don créé avec succès');
        } catch (Exception $e) {
            Flight::redirect('/dons/create?error=' . urlencode($e->getMessage()));
        }
    }

    public function delete($id) {
        try {
            $donRepo = new DonRepository();
            $donRepo->delete($id);
            Flight::redirect('/dons?success=Don supprimé avec succès');
        } catch (Exception $e) {
            Flight::redirect('/dons?error=' . urlencode($e->getMessage()));
        }
    }
}
