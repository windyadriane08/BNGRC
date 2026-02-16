CREATE DATABASE IF NOT EXISTS bngrc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bngrc;

CREATE TABLE IF NOT EXISTS villes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS besoins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    type ENUM('nature', 'materiaux', 'argent') NOT NULL,
    designation VARCHAR(100) NOT NULL,
    quantite_besoin DECIMAL(10, 2) NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    quantite_couverte DECIMAL(10, 2) DEFAULT 0,
    statut ENUM('en_attente', 'partiellement_couverte', 'couverte') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE CASCADE,
    INDEX (ville_id, type)
);

CREATE TABLE IF NOT EXISTS dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('nature', 'materiaux', 'argent') NOT NULL,
    designation VARCHAR(100) NOT NULL,
    quantite_disponible DECIMAL(10, 2) NOT NULL,
    quantite_distribuee DECIMAL(10, 2) DEFAULT 0,
    statut ENUM('non_distribue', 'partiellement_distribue', 'distribue') DEFAULT 'non_distribue',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (type, statut)
);

CREATE TABLE IF NOT EXISTS attributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    besoin_id INT NOT NULL,
    ville_id INT NOT NULL,
    quantite_attribuee DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (don_id) REFERENCES dons(id) ON DELETE CASCADE,
    FOREIGN KEY (besoin_id) REFERENCES besoins(id) ON DELETE CASCADE,
    FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE CASCADE,
    INDEX (don_id, besoin_id, ville_id)
);

-- Insertion de données de test
INSERT INTO villes (nom, description) VALUES
('Antananarivo', 'Capitale'),
('Antsirabe', 'Région Amoron\'i Mania'),
('Fianarantsoa', 'Région Vakinankaratra');

INSERT INTO besoins (ville_id, type, designation, quantite_besoin, prix_unitaire) VALUES
(1, 'nature', 'Riz', 100, 3000),
(1, 'nature', 'Huile', 50, 25000),
(2, 'materiaux', 'Tôle', 200, 15000),
(2, 'argent', 'Secours', 500000, 1),
(3, 'nature', 'Sucre', 75, 2500);

INSERT INTO dons (type, designation, quantite_disponible) VALUES
('nature', 'Riz', 60),
('nature', 'Huile', 30),
('materiaux', 'Tôle', 150),
('argent', 'Secours', 300000);
