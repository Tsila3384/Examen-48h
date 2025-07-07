<?php
require 'vendor/autoload.php';
require 'db.php';
require 'controllers/UserController.php';
require 'controllers/AuthController.php';
require 'controllers/TypePretController.php';

session_start();

$base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($base_url, '/');
define('BASE_URL', $base_url === '' ? '' : $base_url);

$authController = new AuthController();
$typePretController = new TypePretController();

// Middleware de vérification de rôle
Flight::before('start', function() use ($authController) {
    $publicRoutes = ['/auth/connexion', '/auth/inscription'];
    $current = strtok($_SERVER['REQUEST_URI'], '?');
    
    if (!in_array($current, $publicRoutes) && !isset($_SESSION['user'])) {
        Flight::redirect('/auth/connexion');
    }
});

// Routes publiques
Flight::route('GET /auth/connexion', [$authController, 'afficherConnexion']);
Flight::route('GET /auth/inscription', [$authController, 'afficherInscription']);
Flight::route('POST /auth/connexion', [$authController, 'connexion']);
Flight::route('POST /auth/inscription', [$authController, 'inscription']);
Flight::route('POST /auth/deconnexion', [$authController, 'deconnexion']);

// Routes admin
Flight::route('GET /admin/dashboard', function() use ($authController) {
    $authController->verifierRole('admin');
    Flight::render('admin/template/template', ['page' => 'dashboard']);
});



// Routes client
Flight::route('GET /client/dashboard', function() use ($authController) {
    $authController->verifierRole('client');
    Flight::render('client/template/template', ['page' => 'dashboard']);
});

Flight::route('GET /client/types-pret', function() use ($typePretController) {
    $typePretController->getTypesByUser($_SESSION['id']);
});

// Routes pour les types de prêt
Flight::route('GET /admin/types-pret', [$typePretController, 'getAllTypes']);
Flight::route('GET /admin/types-pret/create', [$typePretController, 'create']);
Flight::route('POST /admin/types-pret', [$typePretController, 'store']);
Flight::route('GET /admin/types-pret/edit/@id', [$typePretController, 'edit']);
Flight::route('POST /admin/types-pret/update/@id', [$typePretController, 'update']);
Flight::route('POST /admin/types-pret/delete/@id', [$typePretController, 'destroy']);
// Route par défaut
Flight::route('GET /', function() {
    if (isset($_SESSION['user'])) {
        $redirect = $_SESSION['user']['role'] === 'admin' ? '/admin/dashboard' : '/client/dashboard';
        Flight::redirect($redirect);
    } else {
        Flight::redirect('/auth/connexion');
    }
});

<<<<<<< Updated upstream
=======
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

$userController = new UserController();


Flight::route('POST /user/ajouterFond', [$userController, 'ajouterFonds']);
Flight::route('GET /user/formulaireFond', [$userController, 'formulaireAjoutFonds']);
Flight::route('GET /admin/types-pret', function() {
    $controller = new TypePretController();
    $controller->getAllTypes();
});
Flight::route('GET /client/types-pret', function() {
    if (!isset($_SESSION['user'])) {
        Flight::redirect(BASE_URL . '/login');
        return;
    }
    $controller = new TypePretController();
    $controller->getTypesByUser($_SESSION['user']['id']);
});

>>>>>>> Stashed changes
Flight::start();