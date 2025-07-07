<?php

use Flight\net\Response;

require 'vendor/autoload.php';
require_once 'models/User.php';
require_once 'models/Client.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // Afficher le formulaire de connexion
    public function afficherConnexion()
    {
        Flight::render('auth/connexion');
    }

    // Afficher le formulaire d'inscription
    public function afficherInscription()
    {
        Flight::render('auth/inscription');
    }

    // Traiter la connexion
    public function connexion()
    {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$username || !$password) {
            Flight::json([
                'success' => false,
                'message' => 'Nom d\'utilisateur et mot de passe requis'
            ]);
            return;
        }

        $user = $this->userModel->getUserByUsername($username);

        if ($user && password_verify($password, $user['password']) && $user['is_active']) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $client = new Client();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['id'] = $this->userModel->getIdClient($_SESSION['user_id']);


            Flight::json([
                'success' => true,
                'message' => 'Connexion réussie',
                'role' => $user['role'],
                'redirect' => BASE_URL . ($user['role'] === 'admin' ? '/admin/dashboard' : '/client/types-pret')
            ]);
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Identifiants invalides ou compte désactivé'
            ]);
        }
    }

    // Traiter l'inscription
    public function inscription()
    {
        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;
        $nom = $_POST['nom'] ?? null;
        $salaire = $_POST['salaire'] ?? null;
        $role = 'client';

        if (!$username || !$email || !$password || !$confirmPassword || !$nom || !$salaire) {
            Flight::json([
                'success' => false,
                'message' => 'Tous les champs sont requis'
            ]);
            return;
        }

        if ($password !== $confirmPassword) {
            Flight::json([
                'success' => false,
                'message' => 'Les mots de passe ne correspondent pas'
            ]);
            return;
        }

        if (strlen($password) < 6) {
            Flight::json([
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 6 caractères'
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flight::json([
                'success' => false,
                'message' => 'Email invalide'
            ]);
            return;
        }

        if (!is_numeric($salaire) || $salaire <= 0) {
            Flight::json([
                'success' => false,
                'message' => 'Le salaire doit être un nombre positif'
            ]);
            return;
        }

        if ($this->userModel->getUserByUsername($username)) {
            Flight::json([
                'success' => false,
                'message' => 'Ce nom d\'utilisateur existe déjà'
            ]);
            return;
        }

        if ($this->userModel->getUserByEmail($email)) {
            Flight::json([
                'success' => false,
                'message' => 'Cet email est déjà utilisé'
            ]);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $result = $this->userModel->createUser($username, $email, $hashedPassword, $nom, $salaire, $role);

        if ($result) {
            Flight::json([
                'success' => true,
                'message' => 'Inscription réussie, vous pouvez maintenant vous connecter',
                'redirect' => BASE_URL . '/auth/connexion'
            ]);
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription'
            ]);
        }
    }

    // Déconnexion
    public function deconnexion()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();

        Flight::json([
            'success' => true,
            'message' => 'Déconnexion réussie',
            'redirect' => BASE_URL . '/auth/connexion'
        ]);
    }

    // Vérifier si l'utilisateur est connecté
    public function verifierConnexion()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            Flight::redirect('/auth/connexion');
        }
    }

    // Vérifier le rôle
    public function verifierRole($roleRequis)
    {
        $this->verifierConnexion();

        if ($_SESSION['role'] !== $roleRequis) {
            Flight::json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }
    }
}
