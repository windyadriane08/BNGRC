<?php

class BesoinRepository {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function getAll() {
        $stmt = $this->db->query('
            SELECT * FROM v_besoins_details
            ORDER BY date_saisie DESC
        ');
        return $stmt->fetchAll();
    }

    public function getByVille($ville_id) {
        $stmt = $this->db->prepare('
            SELECT * FROM v_besoins_details
            WHERE id_ville = ? 
            ORDER BY date_saisie DESC
        ');
        $stmt->execute([$ville_id]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare('SELECT * FROM bngrc_besoin WHERE id_besoin = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($ville_id, $type_id, $quantite, $prix_unitaire) {
        $stmt = $this->db->prepare('
            INSERT INTO bngrc_besoin (id_ville, id_type, quantite, prix_unitaire)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$ville_id, $type_id, $quantite, $prix_unitaire]);
        return $this->db->lastInsertId();
    }

    public function getBesoinsRestants($type_id = null) {
        $query = '
            SELECT *, (quantite_restante * prix_unitaire) AS valeur_restante
            FROM v_besoins_restants
            WHERE quantite_restante > 0
        ';
        $params = [];
        
        if ($type_id) {
            $query .= ' AND id_type = ?';
            $params[] = $type_id;
        }

        $query .= ' ORDER BY id_besoin ASC';
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getBesoinsRestantsById($id_besoin) {
        $stmt = $this->db->prepare('
            SELECT *, (quantite_restante * prix_unitaire) AS valeur_restante
            FROM v_besoins_restants
            WHERE id_besoin = ?
        ');
        $stmt->execute([$id_besoin]);
        return $stmt->fetch();
    }

    public function getTypeRessources() {
        $stmt = $this->db->query('SELECT * FROM bngrc_type_ressource ORDER BY categorie, nom');
        return $stmt->fetchAll();
    }

    public function getTypeById($id) {
        $stmt = $this->db->prepare('SELECT * FROM bngrc_type_ressource WHERE id_type = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createType($nom, $categorie) {
        $stmt = $this->db->prepare('INSERT INTO bngrc_type_ressource (nom, categorie) VALUES (?, ?)');
        $stmt->execute([$nom, $categorie]);
        return $this->db->lastInsertId();
    }
}
