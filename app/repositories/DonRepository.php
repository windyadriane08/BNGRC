<?php

class DonRepository {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function getAll() {
        $stmt = $this->db->query('
            SELECT * FROM v_dons_details
            ORDER BY date_don DESC
        ');
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare('SELECT * FROM bngrc_don WHERE id_don = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($type_id, $quantite) {
        $stmt = $this->db->prepare('
            INSERT INTO bngrc_don (id_type, quantite)
            VALUES (?, ?)
        ');
        $stmt->execute([$type_id, $quantite]);
        return $this->db->lastInsertId();
    }

    public function getQuantiteDistribuee($don_id) {
        $stmt = $this->db->prepare('
            SELECT COALESCE(SUM(quantite_attribuee), 0) as total
            FROM bngrc_attribution
            WHERE id_don = ?
        ');
        $stmt->execute([$don_id]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getDonsDisponibles($type_id = null) {
        $query = '
            SELECT d.*, t.nom, t.categorie,
                   d.quantite - COALESCE(SUM(a.quantite_attribuee), 0) as quantite_restante
            FROM bngrc_don d
            JOIN bngrc_type_ressource t ON d.id_type = t.id_type
            LEFT JOIN bngrc_attribution a ON d.id_don = a.id_don
        ';
        $params = [];
        
        if ($type_id) {
            $query .= ' WHERE d.id_type = ?';
            $params[] = $type_id;
        }

        $query .= ' GROUP BY d.id_don HAVING quantite_restante > 0 ORDER BY d.date_don ASC';
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        // Supprimer d'abord les attributions liées
        $stmt = $this->db->prepare('DELETE FROM bngrc_attribution WHERE id_don = ?');
        $stmt->execute([$id]);
        // Supprimer le don
        $stmt = $this->db->prepare('DELETE FROM bngrc_don WHERE id_don = ?');
        return $stmt->execute([$id]);
    }
}
