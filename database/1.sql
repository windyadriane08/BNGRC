create database bngrc;
use bngrc;

CREATE TABLE bngrc_ville (
    id_ville INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    region VARCHAR(100) NOT NULL
);

CREATE TABLE bngrc_type_ressource (
    id_type INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    categorie ENUM('nature', 'materiaux', 'argent') NOT NULL
);

CREATE TABLE bngrc_besoin (
    id_besoin INT AUTO_INCREMENT PRIMARY KEY,
    id_ville INT NOT NULL,
    id_type INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_ville) REFERENCES bngrc_ville(id_ville),
    FOREIGN KEY (id_type) REFERENCES bngrc_type_ressource(id_type)
);

CREATE TABLE bngrc_don (
    id_don INT AUTO_INCREMENT PRIMARY KEY,
    id_type INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_type) REFERENCES bngrc_type_ressource(id_type)
);

CREATE TABLE bngrc_attribution (
    id_attribution INT AUTO_INCREMENT PRIMARY KEY,
    id_don INT NOT NULL,
    id_besoin INT NOT NULL,
    quantite_attribuee DECIMAL(10,2) NOT NULL,
    date_attribution DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_don) REFERENCES bngrc_don(id_don),
    FOREIGN KEY (id_besoin) REFERENCES bngrc_besoin(id_besoin)
);

CREATE VIEW v_besoins_details AS
SELECT 
    b.id_besoin,
    b.id_ville,
    b.id_type,
    v.nom AS ville,
    t.nom AS ressource,
    t.categorie,
    b.quantite,
    b.prix_unitaire,
    (b.quantite * b.prix_unitaire) AS valeur_totale,
    b.date_saisie
FROM bngrc_besoin as b
JOIN bngrc_ville as v ON b.id_ville = v.id_ville
JOIN bngrc_type_ressource as t ON b.id_type = t.id_type;

CREATE VIEW v_dons_details AS
SELECT
    d.id_don,
    t.nom AS ressource,
    t.categorie,
    d.quantite,
    d.date_don
FROM bngrc_don as d
JOIN bngrc_type_ressource as t ON d.id_type = t.id_type;

CREATE VIEW v_attributions_details AS
SELECT
    a.id_attribution,
    v.nom AS ville,
    t.nom AS ressource,
    a.quantite_attribuee,
    a.date_attribution
FROM bngrc_attribution a
JOIN bngrc_besoin as b ON a.id_besoin = b.id_besoin
JOIN bngrc_ville as v ON b.id_ville = v.id_ville
JOIN bngrc_type_ressource as t ON b.id_type = t.id_type;

CREATE VIEW v_total_attribue AS
SELECT
    id_besoin,
    SUM(quantite_attribuee) AS total_attribue
FROM bngrc_attribution
GROUP BY id_besoin;

CREATE VIEW v_besoins_restants AS
SELECT
    b.id_besoin,
    b.id_ville,
    b.id_type,
    v.nom AS ville,
    t.nom AS ressource,
    b.quantite AS besoin_initial,
    COALESCE(ta.total_attribue, 0) AS quantite_attribuee,
    b.quantite - COALESCE(ta.total_attribue, 0) AS quantite_restante,
    b.prix_unitaire
FROM bngrc_besoin b
JOIN bngrc_ville as v ON b.id_ville = v.id_ville
JOIN bngrc_type_ressource as t ON b.id_type = t.id_type
LEFT JOIN v_total_attribue as ta ON b.id_besoin = ta.id_besoin;

CREATE VIEW v_valeur_restante AS
SELECT
    ville,
    ressource,
    quantite_restante,
    prix_unitaire,
    quantite_restante * prix_unitaire AS valeur_restante
FROM v_besoins_restants;

-- Configuration système (frais d'achat)
CREATE TABLE bngrc_config (
    cle VARCHAR(50) PRIMARY KEY,
    valeur VARCHAR(100) NOT NULL
);

INSERT INTO bngrc_config (cle, valeur) VALUES ('frais_achat_pct', '10');

-- Achats via dons en argent
CREATE TABLE bngrc_achat (
    id_achat INT AUTO_INCREMENT PRIMARY KEY,
    id_besoin INT NOT NULL,
    quantite_achetee DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    frais_pct DECIMAL(5,2) NOT NULL,
    montant_total DECIMAL(12,2) NOT NULL,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_besoin) REFERENCES bngrc_besoin(id_besoin)
);

-- Vue des achats avec détails
CREATE VIEW v_achats_details AS
SELECT
    a.id_achat,
    b.id_ville,
    v.nom AS ville,
    t.nom AS ressource,
    t.categorie,
    a.quantite_achetee,
    a.prix_unitaire,
    a.frais_pct,
    a.montant_total,
    a.date_achat
FROM bngrc_achat a
JOIN bngrc_besoin b ON a.id_besoin = b.id_besoin
JOIN bngrc_ville v ON b.id_ville = v.id_ville
JOIN bngrc_type_ressource t ON b.id_type = t.id_type;

-- Vue des dons en argent disponibles
CREATE VIEW v_dons_argent_disponibles AS
SELECT 
    d.id_don,
    d.quantite,
    COALESCE(SUM(att.quantite_attribuee), 0) AS utilise_attribution,
    COALESCE((SELECT SUM(ac.montant_total) FROM bngrc_achat ac), 0) AS utilise_achats,
    d.quantite - COALESCE(SUM(att.quantite_attribuee), 0) AS disponible
FROM bngrc_don d
JOIN bngrc_type_ressource t ON d.id_type = t.id_type
LEFT JOIN bngrc_attribution att ON d.id_don = att.id_don
WHERE t.categorie = 'argent'
GROUP BY d.id_don;

-- =============================================
-- DONNÉES INITIALES (SEED DATA)
-- =============================================

-- Types de ressources
INSERT INTO bngrc_type_ressource (nom, categorie) VALUES 
('Riz', 'nature'),
('Eau potable', 'nature'),
('Médicaments', 'nature'),
('Couvertures', 'materiaux'),
('Tentes', 'materiaux'),
('Vêtements', 'materiaux'),
('Outils de construction', 'materiaux'),
('Argent', 'argent');

-- Villes
INSERT INTO bngrc_ville (nom, region) VALUES 
('Antananarivo', 'Analamanga'),
('Toamasina', 'Atsinanana'),
('Antsirabe', 'Vakinankaratra'),
('Mahajanga', 'Boeny'),
('Fianarantsoa', 'Haute Matsiatra'),
('Toliara', 'Atsimo-Andrefana');

-- Besoins initiaux
INSERT INTO bngrc_besoin (id_ville, id_type, quantite, prix_unitaire) VALUES 
(1, 1, 500, 2500),    -- Antananarivo: 500 kg de riz
(1, 2, 1000, 500),    -- Antananarivo: 1000 L d'eau
(2, 1, 300, 2500),    -- Toamasina: 300 kg de riz
(2, 4, 200, 15000),   -- Toamasina: 200 couvertures
(3, 3, 100, 5000),    -- Antsirabe: 100 médicaments
(3, 5, 50, 150000),   -- Antsirabe: 50 tentes
(4, 1, 400, 2500),    -- Mahajanga: 400 kg de riz
(4, 6, 500, 8000),    -- Mahajanga: 500 vêtements
(5, 2, 800, 500),     -- Fianarantsoa: 800 L d'eau
(5, 7, 100, 25000);   -- Fianarantsoa: 100 outils

-- Dons reçus
INSERT INTO bngrc_don (id_type, quantite) VALUES 
(1, 200),    -- 200 kg de riz
(1, 150),    -- 150 kg de riz
(2, 500),    -- 500 L d'eau
(4, 100),    -- 100 couvertures
(3, 50),     -- 50 médicaments
(5, 20),     -- 20 tentes
(6, 200),    -- 200 vêtements
(8, 5000000); -- 5 000 000 Ar en argent