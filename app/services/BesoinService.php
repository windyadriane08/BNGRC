<?php

class BesoinService {
    private $besoinRepo;

    public function __construct() {
        $this->besoinRepo = new BesoinRepository();
    }

    public function creerBesoin($ville_id, $type, $designation, $quantite_besoin, $prix_unitaire) {
        if ($quantite_besoin <= 0) {
            throw new Exception('Quantité doit être positive');
        }
        if ($prix_unitaire < 0) {
            throw new Exception('Prix unitaire ne peut pas être négatif');
        }

        return $this->besoinRepo->create($ville_id, $type, $designation, $quantite_besoin, $prix_unitaire);
    }

    public function getBesoinsByVille($ville_id) {
        return $this->besoinRepo->getByVille($ville_id);
    }

    public function getMontantTotalBesoin($besoin_id) {
        $besoin = $this->besoinRepo->getById($besoin_id);
        return $besoin['quantite_besoin'] * $besoin['prix_unitaire'];
    }

    public function getMontantRestantBesoin($besoin_id) {
        $besoin = $this->besoinRepo->getById($besoin_id);
        $quantite_manquante = $besoin['quantite_besoin'] - $besoin['quantite_couverte'];
        return $quantite_manquante * $besoin['prix_unitaire'];
    }
}
