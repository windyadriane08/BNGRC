<?php

class DonService {
    private $donRepo;

    public function __construct() {
        $this->donRepo = new DonRepository();
    }

    public function creerDon($type, $designation, $quantite_disponible) {
        if ($quantite_disponible <= 0) {
            throw new Exception('Quantité doit être positive');
        }

        return $this->donRepo->create($type, $designation, $quantite_disponible);
    }

    public function getDons() {
        return $this->donRepo->getAll();
    }

    public function getTotalDonsDistribues() {
        $dons = $this->donRepo->getAll();
        $total = 0;
        foreach ($dons as $don) {
            $total += $don['quantite_distribuee'];
        }
        return $total;
    }
}
