<?php

class VilleRepository {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function getAll() {
        $stmt = $this->db->query('SELECT * FROM bngrc_ville ORDER BY nom ASC');
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare('SELECT * FROM bngrc_ville WHERE id_ville = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($nom, $region = '') {
        $stmt = $this->db->prepare('INSERT INTO bngrc_ville (nom, region) VALUES (?, ?)');
        $stmt->execute([$nom, $region]);
        return $this->db->lastInsertId();
    }

    public function update($id, $nom, $region = '') {
        $stmt = $this->db->prepare('UPDATE bngrc_ville SET nom = ?, region = ? WHERE id_ville = ?');
        return $stmt->execute([$nom, $region, $id]);
    }

    public function hasBesoins($id) {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM bngrc_besoin WHERE id_ville = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return ($row && (int)$row['c'] > 0);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM bngrc_ville WHERE id_ville = ?');
        return $stmt->execute([$id]);
    }

    public function getWithStats($id) {
        $ville = $this->getById($id);
        if (!$ville) return null;

        $stmt = $this->db->prepare('
            SELECT 
                COUNT(*) as total_besoins,
                SUM(b.quantite * b.prix_unitaire) as total_besoin_montant,
                SUM(COALESCE(ta.total_attribue, 0) * b.prix_unitaire) as total_couvert_montant
            FROM bngrc_besoin b
            LEFT JOIN v_total_attribue ta ON b.id_besoin = ta.id_besoin
            WHERE b.id_ville = ?
        ');
        $stmt->execute([$id]);
        $stats = $stmt->fetch();

        $ville['stats'] = $stats;
        return $ville;
    }
}
