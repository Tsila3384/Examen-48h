<?php
require_once('controllers/UserController.php');
require_once('controllers/PretController.php');

// Définition universelle des chemins
$base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', rtrim($base_url, '/'));
define('BASE_PATH', __DIR__);

Flight::route('GET /etudiants', function() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM etudiant");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

// Auth routes
Flight::route('GET /login', function() {
    include BASE_PATH . '/views/auth/login.php';
});

Flight::route('GET /client', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
        Flight::redirect(BASE_URL . '/login');
        return;
    }
    include BASE_PATH . '/views/client.php';
});

Flight::route('GET /admin', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        Flight::redirect(BASE_URL . '/login');
        return;
    }
    include BASE_PATH . '/views/admin.php';
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
Flight::route('GET /admin/dashboard', function() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
    $page = 'dashboard';
    include BASE_PATH . '/views/admin/template/template.php';
});
$userController = new UserController();
$pretController = new PretController();

Flight::route('POST /user/ajouterFond', [$userController, 'ajouterFonds']);
Flight::route('GET /user/formulaireFond', [$userController, 'formulaireAjoutFonds']);
Flight::route('GET /pret/listePret', [$pretController, 'listePrets']);
Flight::route('POST /pret/approuverPret', [$pretController, 'approuverPret']);
Flight::route('POST /pret/valider', [$pretController, 'validerPret']);
Flight::start();