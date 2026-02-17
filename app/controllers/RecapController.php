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
        
        $totalBesoinsMontant = 0;
        $totalSatisfaitsMontant = 0;
        $totalRestantsMontant = 0;
        $totalAchatsGlobal = 0;
        
        foreach ($villes as $ville) {
            $villeId = $ville['id_ville'];
            $villeName = $ville['nom'];
            
            // Besoins de cette ville
            $besoins = $this->besoinRepository->getByVille($villeId);
            $villeBesoinsMontant = 0;
            $villeSatisfaitsMontant = 0;
            $villeRestantsMontant = 0;
            
            foreach ($besoins as $besoin) {
                $quantiteBesoin = $besoin['quantite'];
                $prixUnitaire = $besoin['prix_unitaire'];
                $montantBesoin = $quantiteBesoin * $prixUnitaire;
                $villeBesoinsMontant += $montantBesoin;
                
                // Attributions pour ce besoin
                $attributions = $this->attributionRepository->getByBesoin($besoin['id_besoin']);
                $attribue = 0;
                foreach ($attributions as $attr) {
                    $attribue += $attr['quantite_attribuee'];
                }
                
                // Achats pour ce besoin (retourne directement le total en quantité)
                $achete = $this->achatRepository->getAchatsPourBesoin($besoin['id_besoin']);
                
                $satisfait = $attribue + $achete;
                $quantiteSatisfaite = min($satisfait, $quantiteBesoin);
                $quantiteRestante = max(0, $quantiteBesoin - $satisfait);
                
                $villeSatisfaitsMontant += $quantiteSatisfaite * $prixUnitaire;
                $villeRestantsMontant += $quantiteRestante * $prixUnitaire;
            }
            
            // Achats de cette ville (montant total)
            $achatsVille = $this->achatRepository->getAll($villeId);
            $villeAchats = 0;
            foreach ($achatsVille as $achat) {
                $villeAchats += $achat['montant_total'];
            }
            
            $recapParVille[] = [
                'id_ville' => $villeId,
                'nom_ville' => $villeName,
                'total_besoins' => $villeBesoinsMontant,
                'total_satisfaits' => $villeSatisfaitsMontant,
                'total_restants' => $villeRestantsMontant,
                'pourcentage_couverture' => $villeBesoinsMontant > 0 ? round(($villeSatisfaitsMontant / $villeBesoinsMontant) * 100, 1) : 100,
                'total_achats' => $villeAchats
            ];
            
            $totalBesoinsMontant += $villeBesoinsMontant;
            $totalSatisfaitsMontant += $villeSatisfaitsMontant;
            $totalRestantsMontant += $villeRestantsMontant;
            $totalAchatsGlobal += $villeAchats;
        }
        
        // Totaux globaux des dons
        $dons = $this->donRepository->getAll();
        $totalDons = 0;
        foreach ($dons as $don) {
            $totalDons += $don['quantite'];
        }
        
        // Argent disponible global
        $argentDisponible = $this->achatRepository->getArgentDisponible();
        
        // Total attribué
        $attributions = $this->attributionRepository->getAll();
        $totalAttribues = 0;
        foreach ($attributions as $attr) {
            $totalAttribues += $attr['quantite_attribuee'];
        }
        
        return [
            'par_ville' => $recapParVille,
            'totaux' => [
                'besoins' => $totalBesoinsMontant,
                'satisfaits' => $totalSatisfaitsMontant,
                'restants' => $totalRestantsMontant,
                'pourcentage_couverture' => $totalBesoinsMontant > 0 ? round(($totalSatisfaitsMontant / $totalBesoinsMontant) * 100, 1) : 100,
                'dons' => $totalDons,
                'attribues' => $totalAttribues,
                'achats' => $totalAchatsGlobal,
                'argent_disponible' => $argentDisponible
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
