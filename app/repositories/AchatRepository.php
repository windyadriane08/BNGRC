<?php

class AchatRepository {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function getAll($ville_id = null) {
        $query = 'SELECT * FROM v_achats_details';
        $params = [];
        if ($ville_id) {
            $query .= ' WHERE id_ville = ?';
            $params[] = $ville_id;
        }
        $query .= ' ORDER BY date_achat DESC';
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($id_besoin, $quantite, $prix_unitaire, $frais_pct) {
        $montant_ht = $quantite * $prix_unitaire;
        $montant_total = $montant_ht * (1 + $frais_pct / 100);
        
        $stmt = $this->db->prepare('
            INSERT INTO bngrc_achat (id_besoin, quantite_achetee, prix_unitaire, frais_pct, montant_total)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([$id_besoin, $quantite, $prix_unitaire, $frais_pct, $montant_total]);
        return $this->db->lastInsertId();
    }

    public function getTotalAchats() {
        $stmt = $this->db->query('SELECT COALESCE(SUM(montant_total), 0) AS total FROM bngrc_achat');
        $row = $stmt->fetch();
        return (float) $row['total'];
    }

    public function getArgentDisponible() {
        // Somme des dons en argent - total des achats
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(d.quantite), 0) AS total_dons
            FROM bngrc_don d
            JOIN bngrc_type_ressource t ON d.id_type = t.id_type
            WHERE t.categorie = 'argent'
        ");
        $row = $stmt->fetch();
        $total_dons = (float) $row['total_dons'];
        
        $total_achats = $this->getTotalAchats();
        
        return $total_dons - $total_achats;
    }

    public function getAchatsPourBesoin($id_besoin) {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(quantite_achetee), 0) AS total FROM bngrc_achat WHERE id_besoin = ?');
        $stmt->execute([$id_besoin]);
        $row = $stmt->fetch();
        return (float) $row['total'];
    }
}
