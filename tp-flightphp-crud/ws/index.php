<?php
require 'vendor/autoload.php';
require 'db.php';
require 'controllers/AuthController.php';

// Démarrer la session
session_start();
require_once('controllers/UserController.php');
require_once('controllers/PretController.php');


$base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($base_url, '/');
define('BASE_URL', $base_url === '' ? '' : $base_url);

$userController = new UserController();
$authController = new AuthController();

// Routes d'authentification
Flight::route('GET /auth/connexion', [$authController, 'afficherConnexion']);
Flight::route('GET /auth/inscription', [$authController, 'afficherInscription']);
Flight::route('POST /auth/connexion', [$authController, 'connexion']);
Flight::route('POST /auth/inscription', [$authController, 'inscription']);
Flight::route('POST /auth/deconnexion', [$authController, 'deconnexion']);

// Routes utilisateur (protégées)
Flight::route('POST /user/ajouterFond', function() use ($userController, $authController) {
    $authController->verifierRole('admin');
    $userController->ajouterFonds();
});

Flight::route('GET /user/formulaireFond', function() use ($userController, $authController) {
    $authController->verifierRole('admin');
    $userController->formulaireAjoutFonds();
});

// Routes admin
Flight::route('GET /admin/dashboard', function() use ($authController) {
    $authController->verifierRole('admin');
    $page = 'dashboard';
    Flight::render('admin/template/template', [
        'page' => $page,
        'user' => $_SESSION
    ]);
});
$userController = new UserController();
$pretController = new PretController();

Flight::route('POST /user/ajouterFond', [$userController, 'ajouterFonds']);
Flight::route('GET /user/formulaireFond', [$userController, 'formulaireAjoutFonds']);
Flight::route('GET /pret/listePret', [$pretController, 'listePrets']);
Flight::route('POST /pret/approuverPret', [$pretController, 'approuverPret']);
Flight::route('POST /pret/valider', [$pretController, 'validerPret']);
// Routes client
Flight::route('GET /client/dashboard', function() use ($authController) {
    $authController->verifierRole('client');
    $page = 'dashboard';
    Flight::render('client/template/template', [
        'page' => $page,
        'user' => $_SESSION
    ]);
});

// Redirection par défaut
Flight::route('GET /', function() {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        $redirect = $_SESSION['role'] === 'admin' ? '/admin/dashboard' : '/client/dashboard';
        Flight::redirect($redirect);
    } else {
        Flight::redirect('/auth/connexion');
    }
});

Flight::route('POST /user/ajouterFond', [$userController, 'ajouterFonds']);
Flight::route('GET /user/formulaireFond', [$userController, 'formulaireAjoutFonds']);
Flight::route('GET /pret/listePret', [$pretController, 'listePrets']);
Flight::route('POST /pret/approuverPret', [$pretController, 'approuverPret']);
Flight::route('POST /pret/valider', [$pretController, 'validerPret']);
Flight::route('GET /client/prets/formulairePret', [$pretController, 'afficherFormPret']);
Flight::route('POST /client/pret/demandePret', [$pretController, 'demandePret']);
Flight::route('GET /admin/interets', [$pretController, 'afficherListeInteretsParMois']);
Flight::route('GET /admin/interets/ajax', [$pretController, 'afficherListeInteretsParMoisAjax']);

Flight::start();