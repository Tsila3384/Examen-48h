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
    id PRIMARY KEY AUTO_INCREMENT,
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
CREATE VIEW view_taux_pret AS
SELECT 
    p.id as pret_id,
    p.client_id,
    p.type_pret_id,
    t.taux_interet as taux
FROM prets p
JOIN clients c ON p.client_id = c.id
JOIN type_client tc ON c.type_client_id = tc.id
JOIN taux t ON t.type_client_id = tc.id AND t.type_pret_id = p.type_pret_id;




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

