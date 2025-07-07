<?php
require_once __DIR__ . '/../models/User.php';


class AuthController {
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
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
                $_SESSION['user'] = $user;
                if ($user['role'] === 'admin') {
                    header('Location: ' . BASE_URL . '/admin/dashboard');
                    exit;
                }
            } else {
                $error = 'Identifiants invalides';
            }
        }
        include __DIR__ . '/../../views/auth/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function loginWS() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $userModel = new User();
        $user = $userModel->findByUsername($username);
        if (!$user) {
            $user = $userModel->findByEmail($username);
        }
        if ($user && $userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user'] = $user;
            echo json_encode([
                'success' => true,
                'user_id' => $user['id'],
                'role' => $user['role']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Identifiants invalides'
            ]);
        }
        exit;
    }
}
