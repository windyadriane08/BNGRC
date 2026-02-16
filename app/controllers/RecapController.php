<?php

class RecapController {
    private $besoinRepository;
    private $donRepository;
    private $attributionRepository;
    private $achatRepository;
    private $villeRepository;
    
    public function __construct() {
        $this->besoinRepository = new BesoinRepository();
        $this->donRepository = new DonRepository();
        $this->attributionRepository = new AttributionRepository();
        $this->achatRepository = new AchatRepository();
        $this->villeRepository = new VilleRepository();
    }
    
    /**
     * Page récapitulatif avec bouton actualiser Ajax
     */
    public function index() {
        $recapData = $this->getRecapData();
        Flight::render('recap/index', [
            'pageTitle' => 'Récapitulatif',
            'recap' => $recapData
        ]);
    }
    
    /**
     * Endpoint Ajax pour actualiser les données
     */
    public function data() {
        $recapData = $this->getRecapData();
        Flight::json($recapData);
    }
    
    /**
     * Calcule toutes les données récapitulatives
     */
    private function getRecapData() {
        $villes = $this->villeRepository->getAll();
        $recapParVille = [];
        
        $totalBesoins = 0;
        $totalSatisfaits = 0;
        $totalRestants = 0;
        $totalDons = 0;
        $totalAttribues = 0;
        $totalAchats = 0;
        
        foreach ($villes as $ville) {
            $villeId = $ville['id_ville'];
            $villeName = $ville['nom'];
            
            // Besoins de cette ville
            $besoins = $this->besoinRepository->getByVille($villeId);
            $villeBesoins = 0;
            $villeSatisfaits = 0;
            $villeRestants = 0;
            
            foreach ($besoins as $besoin) {
                $quantiteBesoin = $besoin['quantite'];
                $villeBesoins += $quantiteBesoin;
                
                // Attributions pour ce besoin
                $attributions = $this->attributionRepository->getByBesoin($besoin['id_besoin']);
                $attribue = 0;
                foreach ($attributions as $attr) {
                    $attribue += $attr['quantite_attribuee'];
                }
                
                // Achats pour ce besoin
                $achats = $this->achatRepository->getAchatsPourBesoin($besoin['id_besoin']);
                $achete = 0;
                foreach ($achats as $achat) {
                    $achete += $achat['quantite_achetee'];
                }
                
                $satisfait = $attribue + $achete;
                $villeSatisfaits += min($satisfait, $quantiteBesoin);
                $villeRestants += max(0, $quantiteBesoin - $satisfait);
            }
            
            // Dons de cette ville
            $dons = $this->donRepository->getByVille($villeId);
            $villeDons = 0;
            $villeAttribues = 0;
            
            foreach ($dons as $don) {
                $villeDons += $don['quantite'];
                // Note: quantite_restante est calculée dans la vue SQL
                $villeAttribues += ($don['quantite'] - ($don['quantite_restante'] ?? $don['quantite']));
            }
            
            // Achats de cette ville (pour combler les besoins)
            $achatsVille = $this->achatRepository->getAll($villeId);
            $villeAchats = 0;
            foreach ($achatsVille as $achat) {
                $villeAchats += $achat['montant_total'];
            }
            
            $recapParVille[] = [
                'id_ville' => $villeId,
                'nom_ville' => $villeName,
                'total_besoins' => $villeBesoins,
                'total_satisfaits' => $villeSatisfaits,
                'total_restants' => $villeRestants,
                'pourcentage_couverture' => $villeBesoins > 0 ? round(($villeSatisfaits / $villeBesoins) * 100, 1) : 100,
                'total_dons' => $villeDons,
                'total_attribues' => $villeAttribues,
                'total_achats' => $villeAchats
            ];
            
            $totalBesoins += $villeBesoins;
            $totalSatisfaits += $villeSatisfaits;
            $totalRestants += $villeRestants;
            $totalDons += $villeDons;
            $totalAttribues += $villeAttribues;
            $totalAchats += $villeAchats;
        }
        
        // Argent disponible global
        $argentDisponible = $this->achatRepository->getArgentDisponible();
        
        return [
            'par_ville' => $recapParVille,
            'totaux' => [
                'besoins' => $totalBesoins,
                'satisfaits' => $totalSatisfaits,
                'restants' => $totalRestants,
                'pourcentage_couverture' => $totalBesoins > 0 ? round(($totalSatisfaits / $totalBesoins) * 100, 1) : 100,
                'dons' => $totalDons,
                'attribues' => $totalAttribues,
                'achats' => $totalAchats,
                'argent_disponible' => $argentDisponible
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
