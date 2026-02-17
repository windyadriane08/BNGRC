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
            $typeSimulation = $this->simulerParType($type['id_type'], $type['nom']);
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

    // ==========================================
    // DISPATCH 2 : Plus petits besoins d'abord
    // ==========================================

    /**
     * Dispatch Mode 2 - Attribue les dons aux plus petits besoins d'abord
     */
    public function dispatcherDonsMode2() {
        $types = $this->besoinRepo->getTypeRessources();
        $resultats = [];

        foreach ($types as $type) {
            $resultats[$type['id_type']] = $this->dispatcherParTypeMode2($type['id_type']);
        }

        return $resultats;
    }

    private function dispatcherParTypeMode2($type_id) {
        $dons = $this->donRepo->getDonsDisponibles($type_id);
        $besoins = $this->besoinRepo->getBesoinsRestants($type_id);

        // Trier les besoins du plus petit au plus grand
        usort($besoins, function($a, $b) {
            return $a['quantite_restante'] <=> $b['quantite_restante'];
        });

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

                $quantite_a_attribuer = min($quantite_restante, $quantite_manquante);

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

    /**
     * Simule le dispatch mode 2 sans créer les attributions
     */
    public function simulerDispatchMode2() {
        $types = $this->besoinRepo->getTypeRessources();
        $simulation = [];
        $totalAttributions = 0;

        foreach ($types as $type) {
            $typeSimulation = $this->simulerParTypeMode2($type['id_type'], $type['nom']);
            if (!empty($typeSimulation['attributions'])) {
                $simulation[] = $typeSimulation;
                $totalAttributions += count($typeSimulation['attributions']);
            }
        }

        return [
            'types' => $simulation,
            'total_attributions' => $totalAttributions,
            'mode' => 2,
            'mode_nom' => 'Plus petits besoins d\'abord'
        ];
    }

    private function simulerParTypeMode2($type_id, $type_nom) {
        $dons = $this->donRepo->getDonsDisponibles($type_id);
        $besoins = $this->besoinRepo->getBesoinsRestants($type_id);

        // Trier les besoins du plus petit au plus grand
        usort($besoins, function($a, $b) {
            return $a['quantite_restante'] <=> $b['quantite_restante'];
        });

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

    // ==========================================
    // DISPATCH 3 : Proportionnel
    // ==========================================

    /**
     * Dispatch Mode 3 - Répartition proportionnelle au poids des besoins
     * Avec gestion des arrondis par décimales décroissantes
     */
    public function dispatcherDonsMode3() {
        $types = $this->besoinRepo->getTypeRessources();
        $resultats = [];

        foreach ($types as $type) {
            $resultats[$type['id_type']] = $this->dispatcherParTypeMode3($type['id_type']);
        }

        return $resultats;
    }

    private function dispatcherParTypeMode3($type_id) {
        $dons = $this->donRepo->getDonsDisponibles($type_id);
        $besoins = $this->besoinRepo->getBesoinsRestants($type_id);

        $attributions = [];
        $reste_total = 0;

        foreach ($dons as $don) {
            $quantite_don = $don['quantite_restante'];
            if ($quantite_don <= 0) continue;

            // Calculer le total des besoins restants
            $total_besoins = array_sum(array_column($besoins, 'quantite_restante'));
            if ($total_besoins <= 0) break;

            // Si le don couvre tout, attribuer tout
            if ($quantite_don >= $total_besoins) {
                foreach ($besoins as $key => $besoin) {
                    if ($besoin['quantite_restante'] <= 0) continue;
                    
                    $this->attributionRepo->create($don['id_don'], $besoin['id_besoin'], $besoin['quantite_restante']);
                    
                    $attributions[] = [
                        'don_id' => $don['id_don'],
                        'besoin_id' => $besoin['id_besoin'],
                        'ville' => $besoin['ville'],
                        'ressource' => $besoin['ressource'],
                        'quantite' => $besoin['quantite_restante']
                    ];
                    $besoins[$key]['quantite_restante'] = 0;
                }
                continue;
            }

            // Calculer les parts proportionnelles (floor uniquement)
            $repartition = [];
            foreach ($besoins as $key => $besoin) {
                if ($besoin['quantite_restante'] <= 0) continue;
                
                $part_exacte = ($besoin['quantite_restante'] / $total_besoins) * $quantite_don;
                $part_entiere = floor($part_exacte);
                
                $repartition[$key] = [
                    'besoin' => $besoin,
                    'attribue' => $part_entiere
                ];
            }

            // Calculer le reste (non distribué)
            $total_distribue = array_sum(array_column($repartition, 'attribue'));
            $reste_total += ($quantite_don - $total_distribue);

            // Créer les attributions
            foreach ($repartition as $key => $rep) {
                if ($rep['attribue'] <= 0) continue;
                
                $this->attributionRepo->create($don['id_don'], $rep['besoin']['id_besoin'], $rep['attribue']);
                
                $attributions[] = [
                    'don_id' => $don['id_don'],
                    'besoin_id' => $rep['besoin']['id_besoin'],
                    'ville' => $rep['besoin']['ville'],
                    'ressource' => $rep['besoin']['ressource'],
                    'quantite' => $rep['attribue']
                ];
                
                // Mettre à jour le besoin
                $besoins[$key]['quantite_restante'] -= $rep['attribue'];
            }
        }

        return [
            'attributions' => $attributions,
            'reste' => $reste_total
        ];
    }

    /**
     * Simule le dispatch mode 3 sans créer les attributions
     */
    public function simulerDispatchMode3() {
        $types = $this->besoinRepo->getTypeRessources();
        $simulation = [];
        $totalAttributions = 0;

        foreach ($types as $type) {
            $typeSimulation = $this->simulerParTypeMode3($type['id_type'], $type['nom']);
            if (!empty($typeSimulation['attributions'])) {
                $simulation[] = $typeSimulation;
                $totalAttributions += count($typeSimulation['attributions']);
            }
        }

        return [
            'types' => $simulation,
            'total_attributions' => $totalAttributions,
            'mode' => 3,
            'mode_nom' => 'Proportionnel'
        ];
    }

    private function simulerParTypeMode3($type_id, $type_nom) {
        $dons = $this->donRepo->getDonsDisponibles($type_id);
        $besoins = $this->besoinRepo->getBesoinsRestants($type_id);

        $attributions = [];
        $reste_total = 0;

        foreach ($dons as $don) {
            $quantite_don = $don['quantite_restante'];
            if ($quantite_don <= 0) continue;

            // Calculer le total des besoins restants
            $total_besoins = array_sum(array_column($besoins, 'quantite_restante'));
            if ($total_besoins <= 0) break;

            // Si le don couvre tout
            if ($quantite_don >= $total_besoins) {
                foreach ($besoins as $key => $besoin) {
                    if ($besoin['quantite_restante'] <= 0) continue;
                    
                    $attributions[] = [
                        'don_id' => $don['id_don'],
                        'don_ville' => $don['ville'] ?? 'N/A',
                        'besoin_id' => $besoin['id_besoin'],
                        'besoin_ville' => $besoin['ville'],
                        'ressource' => $besoin['ressource'],
                        'quantite' => $besoin['quantite_restante']
                    ];
                    $besoins[$key]['quantite_restante'] = 0;
                }
                continue;
            }

            // Calculer les parts proportionnelles (floor uniquement)
            $repartition = [];
            foreach ($besoins as $key => $besoin) {
                if ($besoin['quantite_restante'] <= 0) continue;
                
                $part_exacte = ($besoin['quantite_restante'] / $total_besoins) * $quantite_don;
                $part_entiere = floor($part_exacte);
                
                $repartition[$key] = [
                    'besoin' => $besoin,
                    'attribue' => $part_entiere
                ];
            }

            // Calculer le reste (non distribué)
            $total_distribue = array_sum(array_column($repartition, 'attribue'));
            $reste_total += ($quantite_don - $total_distribue);

            // Enregistrer les attributions simulées
            foreach ($repartition as $key => $rep) {
                if ($rep['attribue'] <= 0) continue;
                
                $attributions[] = [
                    'don_id' => $don['id_don'],
                    'don_ville' => $don['ville'] ?? 'N/A',
                    'besoin_id' => $rep['besoin']['id_besoin'],
                    'besoin_ville' => $rep['besoin']['ville'],
                    'ressource' => $rep['besoin']['ressource'],
                    'quantite' => $rep['attribue']
                ];
                
                $besoins[$key]['quantite_restante'] -= $rep['attribue'];
            }
        }

        return [
            'type_nom' => $type_nom,
            'attributions' => $attributions,
            'reste' => $reste_total
        ];
    }
}
