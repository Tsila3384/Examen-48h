<?php
// ws/controllers/AuthController.php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Client.php';

class AuthController {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function register($data) {
        $userModel = new User($this->db);
        $clientModel = new Client($this->db);
        // Vérifier si l'utilisateur existe déjà
        $existing = $userModel->findByUsernameOrEmail($data['username']);
        if ($existing) {
            return ['success' => false, 'message' => "Nom d'utilisateur ou email déjà utilisé."];
        }
        // Créer l'utilisateur
        $userModel->create($data['username'], $data['email'], $data['password']);
        $user_id = $userModel->getLastInsertId();
        // Créer le client
        $clientModel->create($data['nom'], $data['email'], $data['salaire'], $user_id, $data['type_client']);
        return ['success' => true];
    }

    public function login($usernameOrEmail, $password) {
        $userModel = new User($this->db);
        $user = $userModel->findByUsernameOrEmail($usernameOrEmail);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            return ['success' => true, 'role' => $user['role']];
        }
        return ['success' => false, 'message' => 'Identifiants invalides'];
    }
}
