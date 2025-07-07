<?php

require_once __DIR__ . '/../db.php';

class User {
    protected $table = 'users';
    protected $db;

    public function __construct() {
        $this->db = getDB();
    }

   public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }


    public function findIdType($id){
        $stmt = $this->db->prepare("SELECT type_client_id  FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }


    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function verifyPassword($password, $hash) {
        // Comparaison directe pour mots de passe en clair (debug)
        return $password === $hash;
    }

    public function isActive($id) {
        $stmt = $this->db->prepare(query: "SELECT is_active FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ? $result['is_active'] : false;
    }

    public function ajouterFonds($montant, $dateAjout) {
        $stmt = $this->db->prepare("INSERT INTO historique_fonds (id_etablissement, montant, id_type_operation, date_operation) VALUES (1, ?, 1, ?)");
        $stmt1 = $this->db->prepare("UPDATE etablissement SET fonds_disponibles = fonds_disponibles + ? WHERE id = 1");
        $stmt1->execute([$montant]);
        $stmt->execute([$montant, $dateAjout]);
        return $stmt->rowCount() > 0;
    }

    // Créer un nouvel utilisateur avec transaction pour créer aussi le client
    public function createUser($username, $email, $password, $nom, $salaire, $role = 'client') {
        try {
            $this->db->beginTransaction();
            
            // Créer l'utilisateur
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$username, $email, $password, $role]);
            
            if (!$result || $stmt->rowCount() == 0) {
                throw new Exception("Erreur lors de la création de l'utilisateur");
            }
            
            $userId = $this->db->lastInsertId();
            
            // Créer le client (type_client_id = 1 pour "Particulier" par défaut)
            $stmtClient = $this->db->prepare("INSERT INTO clients (nom, email, salaire, user_id, type_client_id) VALUES (?, ?, ?, ?, 1)");
            $resultClient = $stmtClient->execute([$nom, $email, $salaire, $userId]);
            
            if (!$resultClient || $stmtClient->rowCount() == 0) {
                throw new Exception("Erreur lors de la création du profil client");
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Récupérer un utilisateur par nom d'utilisateur
    public function getUserByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par email
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par ID
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer les informations complètes d'un utilisateur avec son profil client
    public function getUserWithClientInfo($userId) {
        $stmt = $this->db->prepare("
            SELECT u.*, c.nom as client_nom, c.salaire, c.type_client_id, tc.libelle as type_client
            FROM users u
            LEFT JOIN clients c ON u.id = c.user_id
            LEFT JOIN type_client tc ON c.type_client_id = tc.id
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserId() {
        
    }

    // Mettre à jour le statut d'un utilisateur
    public function updateUserStatus($id, $isActive) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $result = $stmt->execute([$isActive, $id]);
        return $result && $stmt->rowCount() > 0;
    }

    // Récupérer tous les utilisateurs
    public function getAllUsers() {
        $stmt = $this->db->prepare("SELECT id, username, email, role, created_at, is_active FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les utilisateurs par rôle
    public function getUsersByRole($role) {
        $stmt = $this->db->prepare("SELECT id, username, email, role, created_at, is_active FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIdClient($id) {
        $stmt = $this->db->prepare("SELECT id FROM clients WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }
}