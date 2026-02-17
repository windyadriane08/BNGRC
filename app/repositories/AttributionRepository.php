<?php

class AttributionRepository {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function create($don_id, $besoin_id, $quantite_attribuee) {
        $stmt = $this->db->prepare('
            INSERT INTO bngrc_attribution (id_don, id_besoin, quantite_attribuee)
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$don_id, $besoin_id, $quantite_attribuee]);
    }

    public function getByBesoin($besoin_id) {
        $stmt = $this->db->prepare('
            SELECT a.*, d.id_type, tr.nom as ressource, tr.categorie
            FROM bngrc_attribution a
            LEFT JOIN bngrc_don d ON a.id_don = d.id_don
            LEFT JOIN bngrc_type_ressource tr ON d.id_type = tr.id_type
            WHERE a.id_besoin = ?
            ORDER BY a.date_attribution ASC
        ');
        $stmt->execute([$besoin_id]);
        return $stmt->fetchAll();
    }

    public function getByVille($ville_id) {
        $stmt = $this->db->prepare('
            SELECT * FROM v_attributions_details
            WHERE ville = (SELECT nom FROM bngrc_ville WHERE id_ville = ?)
            ORDER BY date_attribution DESC
        ');
        $stmt->execute([$ville_id]);
        return $stmt->fetchAll();
    }

    public function getAll() {
        $stmt = $this->db->query('
            SELECT * FROM v_attributions_details
            ORDER BY date_attribution DESC
        ');
        return $stmt->fetchAll();
    }

    public function deleteAll() {
        // Supprimer toutes les attributions
        $stmt = $this->db->exec('DELETE FROM bngrc_attribution');
        return true;
    }

    public function resetAll() {
        // Supprimer toutes les attributions et achats
        $this->db->exec('DELETE FROM bngrc_achat');
        $this->db->exec('DELETE FROM bngrc_attribution');
        return true;
    }
}
