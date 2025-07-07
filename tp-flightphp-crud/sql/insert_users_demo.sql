-- Données de test pour la table users (mots de passe en clair)
DELETE FROM users;

INSERT INTO users (username, password, email, role, is_active) VALUES
('admin', 'admin123', 'admin@banque.com', 'admin', 1),
('client', 'client123', 'client@banque.com', 'client', 1),
('alice', 'alicepass', 'alice@banque.com', 'client', 1),
('bob', 'bobpass', 'bob@banque.com', 'client', 1);
