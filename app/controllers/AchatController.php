<?php

class AchatController {
    
    public function list() {
        $ville_id = Flight::request()->query->ville_id ?? null;
        
        $achatRepo = new AchatRepository();
        $villeRepo = new VilleRepository();
        $configRepo = new ConfigRepository();
        $besoinRepo = new BesoinRepository();
        
        $achats = $achatRepo->getAll($ville_id);
        $villes = $villeRepo->getAll();
        $argentDisponible = $achatRepo->getArgentDisponible();
        $fraisPct = $configRepo->getFraisAchat();
        $totalAchats = $achatRepo->getTotalAchats();
        
        // Besoins restants pour proposer les achats
        $besoinsRestants = $besoinRepo->getBesoinsRestants();
        
        Flight::render('achats/list', [
            'pageTitle' => 'Achats',
            'achats' => $achats,
            'villes' => $villes,
            'ville_id_filter' => $ville_id,
            'argentDisponible' => $argentDisponible,
            'fraisPct' => $fraisPct,
            'totalAchats' => $totalAchats,
            'besoinsRestants' => $besoinsRestants
        ]);
    }
    
    public function create($id_besoin) {
        $besoinRepo = new BesoinRepository();
        $achatRepo = new AchatRepository();
        $configRepo = new ConfigRepository();
        $donRepo = new DonRepository();
        
        $besoin = $besoinRepo->getBesoinsRestantsById($id_besoin);
        if (!$besoin) {
            Flight::redirect('/besoins?error=Besoin introuvable');
            return;
        }
        
        // Vérifier si des dons du même type existent encore (règle obligatoire)
        $donsDisponibles = $donRepo->getDonsDisponibles($besoin['id_type']);
        if (count($donsDisponibles) > 0) {
            Flight::redirect('/besoins?error=Achat impossible: des dons de ce type sont encore disponibles. Effectuez d\'abord le dispatch.');
            return;
        }
        
        $fraisPct = $configRepo->getFraisAchat();
        $argentDisponible = $achatRepo->getArgentDisponible();
        $deja_achete = $achatRepo->getAchatsPourBesoin($id_besoin);
        $quantiteRestante = $besoin['quantite_restante'] - $deja_achete;
        
        Flight::render('achats/create', [
            'pageTitle' => 'Nouvel achat',
            'besoin' => $besoin,
            'fraisPct' => $fraisPct,
            'argentDisponible' => $argentDisponible,
            'quantiteRestante' => $quantiteRestante,
            'deja_achete' => $deja_achete
        ]);
    }
    
    public function store() {
        $id_besoin = Flight::request()->data->id_besoin;
        $quantite = (float) Flight::request()->data->quantite_achetee;
        $prix_unitaire_form = (float) Flight::request()->data->prix_unitaire;
        
        $besoinRepo = new BesoinRepository();
        $achatRepo = new AchatRepository();
        $configRepo = new ConfigRepository();
        $donRepo = new DonRepository();
        
        $besoin = $besoinRepo->getBesoinsRestantsById($id_besoin);
        if (!$besoin) {
            Flight::redirect('/achats?error=Besoin introuvable');
            return;
        }
        
        // Vérifier si des dons du même type existent encore
        $donsDisponibles = $donRepo->getDonsDisponibles($besoin['id_type']);
        if (count($donsDisponibles) > 0) {
            Flight::redirect('/besoins?error=Achat impossible: des dons de ce type sont encore disponibles');
            return;
        }
        
        $frais_pct = $configRepo->getFraisAchat();
        $prix_unitaire = $prix_unitaire_form > 0 ? $prix_unitaire_form : $besoin['prix_unitaire'];
        $montant_ht = $quantite * $prix_unitaire;
        $montant_total = $montant_ht * (1 + $frais_pct / 100);
        
        // Vérifier fonds disponibles
        $argent_disponible = $achatRepo->getArgentDisponible();
        if ($montant_total > $argent_disponible) {
            Flight::redirect('/achats/create/' . $id_besoin . '?error=Fonds insuffisants (besoin: ' . number_format($montant_total, 0) . ' Ar, disponible: ' . number_format($argent_disponible, 0) . ' Ar)');
            return;
        }
        
        // Vérifier quantité restante
        $deja_achete = $achatRepo->getAchatsPourBesoin($id_besoin);
        $quantite_restante = $besoin['quantite_restante'] - $deja_achete;
        if ($quantite > $quantite_restante) {
            Flight::redirect('/achats/create/' . $id_besoin . '?error=Quantité demandée supérieure au besoin restant');
            return;
        }
        
        try {
            $achatRepo->create($id_besoin, $quantite, $prix_unitaire, $frais_pct);
            Flight::redirect('/achats?success=Achat enregistré avec succès');
        } catch (Exception $e) {
            Flight::redirect('/achats/create/' . $id_besoin . '?error=' . urlencode($e->getMessage()));
        }
    }
    
    public function config() {
        $configRepo = new ConfigRepository();
        $fraisPct = $configRepo->getFraisAchat();
        
        Flight::render('achats/config', [
            'pageTitle' => 'Configuration Achats',
            'fraisPct' => $fraisPct
        ]);
    }
    
    public function saveConfig() {
        $frais_pct = (float) Flight::request()->data->frais_pct;
        
        $configRepo = new ConfigRepository();
        $configRepo->setFraisAchat($frais_pct);
        
        Flight::redirect('/achats?success=Configuration mise à jour');
    }
}
