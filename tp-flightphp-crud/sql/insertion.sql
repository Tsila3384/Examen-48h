-- Admin
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@banque.com', '$2y$10$Qe1Qw1Qw1Qw1Qw1Qw1Qw1u1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw', 'admin');
-- (mot de passe : admin123)

-- Clients
INSERT INTO users (username, email, password, role) VALUES
('jdupont', 'j.dupont@mail.com', '$2y$10$Qe1Qw1Qw1Qw1Qw1Qw1Qw1u1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw', 'client'),
('mlefevre', 'm.lefevre@mail.com', '$2y$10$Qe1Qw1Qw1Qw1Qw1Qw1Qw1u1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw', 'client');
-- (mot de passe pour tous : client123)


INSERT INTO clients (nom, email, salaire, user_id, type_client_id) VALUES
('Jean Dupont', 'j.dupont@mail.com', 2500.00, 2, 1),
('Marie Lefevre', 'm.lefevre@mail.com', 3200.00, 3, 2);

INSERT INTO prets (id_etablissement, client_id, type_pret_id, montant, id_statut, date_demande, mensualite, duree_mois) VALUES
(1, 1, 1, 10000.00, 1, '2025-07-01', 210.00, 60),
(1, 2, 2, 25000.00, 2, '2025-06-15', 400.00, 84);

 SELECT 
            p.*,
            c.nom AS client_nom,
            c.email AS client_email,
            c.salaire AS client_salaire,
            tc.libelle AS type_client,
            tp.nom AS type_pret,
            s.libelle AS statut,
            v.taux AS taux_interet
        FROM prets p
        JOIN clients c ON p.client_id = c.id
        JOIN type_client tc ON c.type_client_id = tc.id
        JOIN type_pret tp ON p.type_pret_id = tp.id
        JOIN statut s ON p.id_statut = s.id
        JOIN view_taux_pret v ON p.id = v.pret_id
        WHERE p.id = 1;