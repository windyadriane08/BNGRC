<?php

class BesoinController {
    public function list($ville_id = null) {
        $besoinRepo = new BesoinRepository();
        $villeRepo = new VilleRepository();

        $villes = $villeRepo->getAll();

        if ($ville_id) {
            $besoins = $besoinRepo->getByVille($ville_id);
        } else {
            $besoins = $besoinRepo->getAll();
        }

        Flight::render('besoins/list', [
            'besoins' => $besoins,
            'villes' => $villes,
            'ville_id_filter' => $ville_id
        ]);
    }

    public function create() {
        $villeRepo = new VilleRepository();
        $besoinRepo = new BesoinRepository();
        
        $villes = $villeRepo->getAll();
        $types = $besoinRepo->getTypeRessources();

        Flight::render('besoins/create', [
            'villes' => $villes,
            'types' => $types
        ]);
    }

    public function store() {
        $ville_id = Flight::request()->data->ville_id;
        $type_id = Flight::request()->data->type_id;
        $montant = Flight::request()->data->montant; // pour argent
        $quantite = Flight::request()->data->quantite;
        $prix_unitaire = Flight::request()->data->prix_unitaire;

        try {
            $besoinRepo = new BesoinRepository();
            $type = $besoinRepo->getTypeById($type_id);
            if ($type && $type['categorie'] === 'argent') {
                // Montant direct: quantite = montant, prix_unitaire = 1
                $quantite = (float)$montant;
                $prix_unitaire = 1;
            }
            $besoinRepo->create($ville_id, $type_id, $quantite, $prix_unitaire);
            Flight::redirect('/besoins?success=Besoin créé avec succès');
        } catch (Exception $e) {
            Flight::redirect('/besoins/create?error=' . urlencode($e->getMessage()));
        }
    }

    public function delete($id) {
        try {
            $besoinRepo = new BesoinRepository();
            $besoinRepo->delete($id);
            Flight::redirect('/besoins?success=Besoin supprimé avec succès');
        } catch (Exception $e) {
            Flight::redirect('/besoins?error=' . urlencode($e->getMessage()));
        }
    }
}
