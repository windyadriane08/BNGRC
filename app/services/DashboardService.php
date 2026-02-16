<?php

class DashboardService {
    private $villeRepo;
    private $besoinRepo;
    private $donRepo;
    private $attributionRepo;

    public function __construct() {
        $this->villeRepo = new VilleRepository();
        $this->besoinRepo = new BesoinRepository();
        $this->donRepo = new DonRepository();
        $this->attributionRepo = new AttributionRepository();
    }

    /**
     * Récupère les données complètes du tableau de bord
     */
    public function getDashboardData() {
        $villes = $this->villeRepo->getAll();
        $besoins = $this->besoinRepo->getAll();
        $dons = $this->donRepo->getAll();
        $attributions = $this->attributionRepo->getAll();
        $besoinsRestants = $this->besoinRepo->getBesoinsRestants();

        $data = [
            'villes' => [],
            'besoins' => $besoins,
            'besoins_restants' => $besoinsRestants,
            'dons' => $dons,
            'attributions' => $attributions,
            'stats' => $this->calculerStats($villes, $besoins, $dons, $besoinsRestants)
        ];

        foreach ($villes as $ville) {
            $ville['besoins'] = $this->besoinRepo->getByVille($ville['id_ville']);
            $ville['attributions'] = $this->attributionRepo->getByVille($ville['id_ville']);
            $data['villes'][] = $ville;
        }

        return $data;
    }

    /**
     * Calcule les statistiques globales
     */
    public function calculerStats($villes, $besoins, $dons, $besoinsRestants) {
        $stats = [
            'nb_villes' => count($villes),
            'nb_besoins_total' => count($besoins),
            'nb_dons_total' => count($dons),
            'montant_besoins_total' => 0,
            'montant_besoins_restants' => 0,
            'pourcentage_couverture' => 0
        ];

        foreach ($besoins as $besoin) {
            $montant_total = $besoin['valeur_totale'];
            $stats['montant_besoins_total'] += $montant_total;
        }

        foreach ($besoinsRestants as $besoin) {
            $montant_restant = $besoin['quantite_restante'] * $besoin['prix_unitaire'];
            $stats['montant_besoins_restants'] += $montant_restant;
        }

        if ($stats['montant_besoins_total'] > 0) {
            $montant_couvert = $stats['montant_besoins_total'] - $stats['montant_besoins_restants'];
            $stats['pourcentage_couverture'] = round(($montant_couvert / $stats['montant_besoins_total']) * 100, 2);
        }

        return $stats;
    }
}
