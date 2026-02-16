<?php

class VilleController {
    public function list() {
        $villeRepo = new VilleRepository();
        $villes = $villeRepo->getAll();
        
        Flight::render('villes/list', ['villes' => $villes]);
    }

    public function create() {
        Flight::render('villes/create');
    }

    public function store() {
        $nom = Flight::request()->data->nom;
        $region = Flight::request()->data->region ?? '';

        if (!$nom) {
            Flight::redirect('/villes/create?error=Le nom est obligatoire');
            return;
        }

        try {
            $villeRepo = new VilleRepository();
            $villeRepo->create($nom, $region);
            Flight::redirect('/villes?success=Ville créée avec succès');
        } catch (Exception $e) {
            Flight::redirect('/villes/create?error=' . urlencode($e->getMessage()));
        }
    }

    public function delete($id) {
        try {
            $villeRepo = new VilleRepository();
            if ($villeRepo->hasBesoins($id)) {
                Flight::redirect('/villes?error=Suppression impossible: des besoins sont associés à cette ville');
                return;
            }
            $villeRepo->delete($id);
            Flight::redirect('/villes?success=Ville supprimée');
        } catch (Exception $e) {
            Flight::redirect('/villes?error=' . urlencode($e->getMessage()));
        }
    }
}
