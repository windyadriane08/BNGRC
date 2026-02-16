<?php

class ConfigRepository {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public function get($cle, $default = null) {
        $stmt = $this->db->prepare('SELECT valeur FROM bngrc_config WHERE cle = ?');
        $stmt->execute([$cle]);
        $row = $stmt->fetch();
        return $row ? $row['valeur'] : $default;
    }

    public function set($cle, $valeur) {
        $stmt = $this->db->prepare('INSERT INTO bngrc_config (cle, valeur) VALUES (?, ?) ON DUPLICATE KEY UPDATE valeur = ?');
        return $stmt->execute([$cle, $valeur, $valeur]);
    }

    public function getFraisAchat() {
        return (float) $this->get('frais_achat_pct', '10');
    }

    public function setFraisAchat($pct) {
        return $this->set('frais_achat_pct', (string) $pct);
    }
}
