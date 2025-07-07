<?php


// Correction du calcul de $base_url pour garantir le slash initial et pas de slash final
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($base_url === '' || $base_url === '.' || $base_url === '/') $base_url = '';
$base_url = $base_url ? '/' . ltrim($base_url, '/') : '';

// Définir le chemin de base pour toutes les inclusions et les routes
$base_dir = __DIR__;

require 'vendor/autoload.php';

require 'db.php';
require 'controllers/AuthController.php';
require 'controllers/UserController.php';

$base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', rtrim($base_url, '/'));


if (session_status() === PHP_SESSION_NONE) session_start();
$authController = new AuthController($db);

// Redirection automatique vers /login si l'utilisateur n'est pas connecté et n'est pas déjà sur une page d'auth
$publicRoutes = [BASE_URL . '/', BASE_URL . '/login', BASE_URL . '/inscription', BASE_URL . '/api/login'];
$currentUri = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
$publicRoutes = array_map(function($route) { return rtrim($route, '/'); }, $publicRoutes);
if (!isset($_SESSION['user']) && !in_array($currentUri, $publicRoutes)) {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

// Route principale : redirige toujours vers /login si non connecté
Flight::route('GET /', function() {
    if (!isset($_SESSION['user'])) {
        Flight::redirect(BASE_URL . '/login');
        return;
    }
    if ($_SESSION['user']['role'] === 'admin') {
        Flight::redirect(BASE_URL . '/admin');
    } else {
        Flight::redirect(BASE_URL . '/client');
    }
});

// Auth routes
Flight::route('GET /login', function() {
    include __DIR__ . '/views/auth/login.php';
});

Flight::route('GET /client', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
        Flight::redirect(BASE_URL . '/login');
        return;
    }
    include __DIR__ . '/views/client.php';
});

Flight::route('GET /admin', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        Flight::redirect(BASE_URL . '/login');
        return;
    }
    include __DIR__ . '/views/admin.php';
});

Flight::route('GET /logout', function() {
    session_destroy();
    Flight::redirect(BASE_URL . '/login');
});

// Web service login route
Flight::route('POST /api/login', function() {
    $controller = new AuthController();
    $controller->loginWS();
});

// Admin dashboard
Flight::route('GET /admin/dashboard', function() use ($base_dir, $base_url) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . $base_url . '/login');
        exit;
    }
    include $base_dir . '/views/admin/template/template.php';
});

Flight::route('POST /user/ajouterFond', [$userController, 'ajouterFonds']);
Flight::route('GET /user/formulaireFond', [$userController, 'formulaireAjoutFonds']);

Flight::start();