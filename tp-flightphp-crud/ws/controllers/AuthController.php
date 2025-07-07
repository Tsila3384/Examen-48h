<?php
global $base_url;
require_once __DIR__ . '/../models/User.php';


class AuthController {
    public function login() {
        global $base_url;
        session_start();
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $userModel = new User();
            $user = $userModel->findByUsername($username);
            if (!$user) {
                $user = $userModel->findByEmail($username);
            }
            if ($user && $userModel->verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                if ($user['role'] === 'admin') {
                    header('Location: ' . $base_url . '/admin/dashboard');
                    
                    exit;
                } else {
                    header('Location: ' . $base_url . '/client/dashboard');
                    exit;
                }
            } else {
                $error = 'Identifiants invalides';
            }
        }
        include __DIR__ . '/../../views/auth/login.php';
    }

    public function logout() {
        global $base_url;
        session_start();
        session_destroy();
        header('Location: ' . $base_url . '/login');
        exit;
    }

    public function loginWS() {
        global $base_url;
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $userModel = new User();
        $user = $userModel->findByUsername($username);
        if (!$user) {
            $user = $userModel->findByEmail($username);
        }
        if ($user && $userModel->verifyPassword($password, $user['password'])) {
            echo json_encode([
                'success' => true,
                'user_id' => $user['id'],
                'role' => $user['role'],
                'base_url' => $base_url
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Identifiants invalides',
                'base_url' => $base_url
            ]);
        }
        exit;
    }
}
