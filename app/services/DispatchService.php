<?php

class DispatchService {
    private $besoinRepo;
    private $donRepo;
    private $attributionRepo;

    public function __construct() {
        $this->besoinRepo = new BesoinRepository();
        $this->donRepo = new DonRepository();
        $this->attributionRepo = new AttributionRepository();
    }

    /**
     * Dispatcher automatique - attribue les dons aux besoins
     * selon l'ordre chronologique (FIFO)
     */
    public function dispatcherDons() {
        $types = $this->besoinRepo->getTypeRessources();
        $resultats = [];

        foreach ($types as $type) {
            $resultats[$type['id_type']] = $this->dispatcherParType($type['id_type']);
        }

        return $resultats;
    }

    private function dispatcherParType($type_id) {
        $dons = $this->donRepo->getDonsDisponibles($type_id);
        $besoins = $this->besoinRepo->getBesoinsRestants($type_id);

        $attributions = [];

        foreach ($dons as $don) {
            $quantite_restante = $don['quantite_restante'];

            if ($quantite_restante <= 0) continue;

            foreach ($besoins as $key => $besoin) {
                if ($quantite_restante <= 0) break;

                $quantite_manquante = $besoin['quantite_restante'];

                if ($quantite_manquante <= 0) {
                    unset($besoins[$key]);
                    continue;
                }

                // Attribuer
                $quantite_a_attribuer = min($quantite_restante, $quantite_manquante);

                // Créer l'attribution
                $this->attributionRepo->create(
                    $don['id_don'],
                    $besoin['id_besoin'],
                    $quantite_a_attribuer
                );

                $quantite_restante -= $quantite_a_attribuer;
                $besoins[$key]['quantite_restante'] -= $quantite_a_attribuer;

                $attributions[] = [
                    'don_id' => $don['id_don'],
                    'besoin_id' => $besoin['id_besoin'],
                    'ville' => $besoin['ville'],
                    'ressource' => $besoin['ressource'],
                    'quantite' => $quantite_a_attribuer
                ];
            }
        }

        return $attributions;
    }

    public function getResultatDispatch() {
        return [
            'besoins_restants' => $this->besoinRepo->getBesoinsRestants(),
            'dons_disponibles' => $this->donRepo->getDonsDisponibles(),
            'attributions' => $this->attributionRepo->getAll()
        ];
    }

    /**
     * Simule le dispatch sans créer les attributions en base
     * Retourne un aperçu des attributions qui seraient faites
     */
    public function simulerDispatch() {
        $types = $this->besoinRepo->getTypeRessources();
        $simulation = [];
        $totalAttributions = 0;

        foreach ($types as $type) {
            $typeSimulation = $this->simulerParType($type['id_type'], $type['nom_type']);
            if (!empty($typeSimulation['attributions'])) {
                $simulation[] = $typeSimulation;
                $totalAttributions += count($typeSimulation['attributions']);
            }
        }

        return [
            'types' => $simulation,
            'total_attributions' => $totalAttributions
        ];
    }

    private function simulerParType($type_id, $type_nom) {
        $dons = $this->donRepo->getDonsDisponibles($type_id);
        $besoins = $this->besoinRepo->getBesoinsRestants($type_id);

        $attributions = [];

        foreach ($dons as $don) {
            $quantite_restante = $don['quantite_restante'];

            if ($quantite_restante <= 0) continue;

            foreach ($besoins as $key => $besoin) {
                if ($quantite_restante <= 0) break;

                $quantite_manquante = $besoin['quantite_restante'];

                if ($quantite_manquante <= 0) {
                    unset($besoins[$key]);
                    continue;
                }

                // Calculer la quantité à attribuer (sans persister)
                $quantite_a_attribuer = min($quantite_restante, $quantite_manquante);

                $quantite_restante -= $quantite_a_attribuer;
                $besoins[$key]['quantite_restante'] -= $quantite_a_attribuer;

                $attributions[] = [
                    'don_id' => $don['id_don'],
                    'don_ville' => $don['ville'] ?? 'N/A',
                    'besoin_id' => $besoin['id_besoin'],
                    'besoin_ville' => $besoin['ville'],
                    'ressource' => $besoin['ressource'],
                    'quantite' => $quantite_a_attribuer
                ];
            }
        }

        return [
            'type_nom' => $type_nom,
            'attributions' => $attributions
        ];
    }
}
