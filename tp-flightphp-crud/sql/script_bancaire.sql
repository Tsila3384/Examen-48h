-- Active: 1744780646950@@127.0.0.1@3306@banque
CREATE DATABASE IF NOT EXISTS banque;

USE banque;

-- Table des utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'client') DEFAULT 'client',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- etablissement
CREATE TABLE etablissement (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    fonds_disponibles DECIMAL(15,2) DEFAULT 0
);

-- Types de client
CREATE TABLE type_client (
    id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(50) NOT NULL
);

-- Types de prêt
CREATE TABLE type_pret (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50),
    duree_max INT
);

-- Taux d'interêt par type de client et type de prêt
CREATE TABLE taux (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_client_id INT,
    type_pret_id INT,
    taux_interet DECIMAL(5,2),
    FOREIGN KEY(type_client_id) REFERENCES type_client(id),
    FOREIGN KEY(type_pret_id) REFERENCES type_pret(id)
);

-- Clients
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    email VARCHAR(100),
    salaire DECIMAL(10,2),
    user_id INT NOT NULL,
    type_client_id INT NOT NULL,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(type_client_id) REFERENCES type_client(id)
);

-- Statut
CREATE TABLE statut (
    id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100)
);

-- Type operation
CREATE TABLE type_operation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100)
);

-- Prêts
CREATE TABLE prets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_etablissement INT NOT NULL,
    client_id INT,
    type_pret_id INT,
    montant DECIMAL(12,2),
    id_statut INT NOT NULL,
    date_demande DATE,
    duree_mois INT,
    FOREIGN KEY(id_etablissement) REFERENCES etablissement(id),
    FOREIGN KEY(client_id) REFERENCES clients(id),
    FOREIGN KEY(type_pret_id) REFERENCES type_pret(id),
    FOREIGN KEY(id_statut) REFERENCES statut(id)
);

CREATE TABLE mensualite(
    id INT PRIMARY KEY AUTO_INCREMENT,
    pret_id INT NOT NULL,
    client_id INT NOT NULL,
    montant DECIMAL(12,2),
    date_mensualite DATE,
    FOREIGN KEY(pret_id) REFERENCES prets(id),
    FOREIGN KEY(client_id) REFERENCES clients(id)
)

-- Historique des fonds
CREATE TABLE historique_fonds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_etablissement INT,
    montant DECIMAL(15,2),
    id_type_operation INT NOT NULL,
    date_operation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(id_etablissement) REFERENCES etablissement(id),
    FOREIGN KEY(id_type_operation) REFERENCES type_operation(id)
);

--vue recuperation de taux
CREATE OR REPLACE VIEW view_taux_pret AS
SELECT 
    p.id as pret_id,
    p.client_id,
    p.type_pret_id,
    t.taux_interet as taux
FROM prets p
JOIN clients c ON p.client_id = c.id
JOIN type_client tc ON c.type_client_id = tc.id
JOIN taux t ON t.type_client_id = tc.id AND t.type_pret_id = p.type_pret_id;

-- Vue pour les intérêts par mois
CREATE OR REPLACE VIEW view_interet_par_mois AS
SELECT 
    CONCAT(YEAR(m.date_mensualite), '-', LPAD(MONTH(m.date_mensualite), 2, '0')) as AnneeMois,
    SUM(COALESCE(m.montant_interets, 0)) as total_interets,
    SUM(COALESCE(m.montant_capital, 0)) as total_capital,
    SUM(COALESCE(m.montant_assurance, 0)) as total_assurance,
    SUM(COALESCE(m.montant_interets, 0) + COALESCE(m.montant_capital, 0) + COALESCE(m.montant_assurance, 0)) as total_mensualites
FROM mensualite m
GROUP BY YEAR(m.date_mensualite), MONTH(m.date_mensualite)
ORDER BY AnneeMois;

-- Vue groupée par mois : mensualités + fonds disponibles établissement évolutifs
CREATE OR REPLACE VIEW view_mensualites_fonds_par_mois AS
SELECT 
    AnneeMois,
    total_mensualites,
    (fonds_initiaux + cumul_mensualites_precedentes) as fonds_disponibles,
    (total_mensualites + fonds_initiaux + cumul_mensualites_precedentes) as total_mensualites_plus_fonds
FROM (
    SELECT 
        CONCAT(YEAR(m.date_mensualite), '-', LPAD(MONTH(m.date_mensualite), 2, '0')) as AnneeMois,
        SUM(COALESCE(m.montant, 0)) as total_mensualites,
        (SELECT fonds_disponibles FROM etablissement LIMIT 1) as fonds_initiaux,
        (
            SELECT COALESCE(SUM(m2.montant), 0)
            FROM mensualite m2 
            WHERE m2.date_mensualite < DATE(CONCAT(YEAR(m.date_mensualite), '-', MONTH(m.date_mensualite), '-01'))
        ) as cumul_mensualites_precedentes
    FROM mensualite m
    GROUP BY YEAR(m.date_mensualite), MONTH(m.date_mensualite)
) as calculs
ORDER BY AnneeMois;


select * from mensualite;



-- Donnees initiales
INSERT INTO etablissement (nom, fonds_disponibles) VALUES ('Banque Centrale', 1000000.00);

INSERT INTO type_client (libelle) VALUES 
('Particulier'),
('Entreprise');

INSERT INTO statut (libelle) VALUES 
('En attente'),
('Approuve'),
('Rejete'),
('En cours'),
('Termine');

INSERT INTO type_operation (libelle) VALUES
('Ajout'),
('Pret'),
('Remboursement');

INSERT INTO type_pret (nom, duree_max) VALUES 
('Prêt Personnel', 60),
('Prêt Auto', 84),
('Prêt Immobilier', 300);

INSERT INTO taux (type_client_id, type_pret_id, taux_interet) VALUES 
(1, 1, 8.5),  -- Particulier, Prêt Personnel
(1, 2, 6.2),  -- Particulier, Prêt Auto
(1, 3, 3.8),  -- Particulier, Prêt Immobilier
(2, 1, 7.0),  -- Entreprise, Prêt Personnel
(2, 2, 5.5),  -- Entreprise, Prêt Auto
(2, 3, 3.2);  -- Entreprise, Prêt Immobilier

-- Utilisateurs clients
INSERT INTO users (username, password, email, role) VALUES
('client1', 'mdp1', 'client1@mail.com', 'client'),
('client2', 'mdp2', 'client2@mail.com', 'client');


INSERT INTO clients (nom, email, salaire, user_id, type_client_id) VALUES
('Jean Randria', 'client1@mail.com', 850000.00, 1, 1),   -- Particulier
('Société Ando', 'client2@mail.com', 5000000.00, 2, 2); -- Entreprise


-- Prêt 1 : Client 1, prêt personnel, approuvé
INSERT INTO prets (id_etablissement, client_id, type_pret_id, montant, id_statut, date_demande, duree_mois) VALUES
(1, 5, 1, 200000.00, 2, '2025-06-01', 48);

-- Prêt 2 : Client 2, prêt auto, en cours
INSERT INTO prets (id_etablissement, client_id, type_pret_id, montant, id_statut, date_demande, duree_mois) VALUES
(1, 2, 2, 450000.00, 4, '2025-05-15', 72);

-- Prêt 3 : Client 1, prêt immobilier, en attente
INSERT INTO prets (id_etablissement, client_id, type_pret_id, montant, id_statut, date_demande, duree_mois) VALUES
(1, 1, 3, 1200000.00, 1, '2025-07-01', 240);

-- Prêt 4 : Client 2, prêt personnel, rejeté
INSERT INTO prets (id_etablissement, client_id, type_pret_id, montant, id_statut, date_demande, duree_mois) VALUES
(1, 2, 1, 150000.00, 3, '2025-04-10', 36);

-- Prêt 5 : Client 1, prêt auto, terminé
INSERT INTO prets (id_etablissement, client_id, type_pret_id, montant, id_statut, date_demande, duree_mois) VALUES
(1, 1, 2, 300000.00, 5, '2022-01-01', 60);

ALTER TABLE prets ADD taux_assurance DECIMAL(5,2) DEFAULT 0;
ALTER TABLE prets ADD delai_premier_remboursement INT DEFAULT 0;


ALTER TABLE mensualite
ADD montant_capital DECIMAL(10,2) DEFAULT 0,
ADD montant_interets DECIMAL(10,2) DEFAULT 0,
ADD montant_assurance DECIMAL(10,2) DEFAULT 0;

CREATE OR REPLACE VIEW  DetailPret AS
SELECT 
    p.id AS pret_id,
    c.nom AS client_nom,
    c.email AS client_email,
    c.salaire AS client_salaire,
    tc.libelle AS type_client,
    tp.nom AS type_pret,
    s.libelle AS statut,
    v.taux AS taux_interet,
    p.montant AS montant_pret,
    p.date_demande AS date_demande,
    p.duree_mois AS duree_mois,
    m.montant,
    m.montant_assurance,
    m.date_mensualite
FROM prets p
JOIN clients c ON p.client_id = c.id
JOIN type_client tc ON c.type_client_id = tc.id
JOIN type_pret tp ON p.type_pret_id = tp.id
JOIN statut s ON p.id_statut = s.id
JOIN view_taux_pret v ON p.id = v.pret_id
JOIN mensualite m ON p.id = m.pret_id ORDER BY m.date_mensualite ASC;




