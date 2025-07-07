<?php
require 'vendor/autoload.php';
require 'db.php';
require 'controllers/AuthController.php';
require 'controllers/TypePretController.php';

session_start();
require_once('controllers/UserController.php');
require_once('controllers/PretController.php');

$authController = new AuthController();
$typePretController = new TypePretController();
$userController = new UserController();
$pretController = new PretController();


$base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($base_url, '/');
define('BASE_URL', $base_url === '' ? '' : $base_url);

$authController = new AuthController();
$typePretController = new TypePretController();

// Routes publiques
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

// Routes pour les prêts
Flight::route('GET /user/listePret', function() use ($pretController) {
    $pretController->afficherPretByUser($_SESSION['id'] );
});
Flight::route('/user/prets/pdf/@id', function($id){
    $pretController = new PretController();
    $pretController->genererPDF($id);
});


// Routes admin
Flight::route('GET /admin/dashboard', function() use ($authController) {
    $authController->verifierRole('admin');
    Flight::render('admin/template/template', ['page' => 'dashboard']);
});

$userController = new UserController();
$pretController = new PretController();

Flight::route('POST /user/ajouterFond', [$userController, 'ajouterFonds']);
Flight::route('GET /user/formulaireFond', [$userController, 'formulaireAjoutFonds']);
Flight::route('GET /pret/listePret', [$pretController, 'listePrets']);
Flight::route('POST /pret/approuverPret', [$pretController, 'approuverPret']);
Flight::route('POST /pret/valider', [$pretController, 'validerPret']);
// Route par défaut - doit être définie avant les autres routes
Flight::route('GET /', function() {
    if (isset($_SESSION['user'])) {
        $redirect = $_SESSION['user']['role'] === 'admin' ? '/admin/dashboard' : '/client/dashboard';
        Flight::redirect($redirect);
    } else {
        Flight::redirect('/auth/connexion');
    }
});

// Routes client
Flight::route('GET /client/dashboard', function() use ($authController) {
    $authController->verifierRole('client');
    Flight::render('client/template/template', ['page' => 'dashboard']);
});

Flight::route('GET /client/types-pret', function() use ($typePretController) {
    $typePretController->getTypesByUser($_SESSION['id']);
});

Flight::route('GET /client/prets/formulairePret', [$pretController, 'afficherFormPret']);
Flight::route('POST /client/pret/demandePret', [$pretController, 'demandePret']);

// Routes pour les types de prêt (admin)
Flight::route('GET /admin/types-pret', [$typePretController, 'getAllTypes']);
Flight::route('GET /admin/types-pret/create', [$typePretController, 'create']);
Flight::route('POST /admin/types-pret', [$typePretController, 'store']);
Flight::route('GET /admin/types-pret/edit/@id', [$typePretController, 'edit']);
Flight::route('POST /admin/types-pret/update/@id', [$typePretController, 'update']);
Flight::route('POST /admin/types-pret/delete/@id', [$typePretController, 'destroy']);

// Routes pour la gestion des fonds
Flight::route('POST /user/ajouterFond', [$userController, 'ajouterFonds']);
Flight::route('GET /user/formulaireFond', [$userController, 'formulaireAjoutFonds']);

// Routes pour la gestion des prêts
Flight::route('GET /pret/listePret', [$pretController, 'listePrets']);
Flight::route('POST /pret/approuverPret', [$pretController, 'approuverPret']);
Flight::route('POST /pret/valider', [$pretController, 'validerPret']);

// Routes pour les intérêts
Flight::route('GET /admin/interets', [$pretController, 'afficherListeInteretsParMois']);
Flight::route('GET /admin/interets/ajax', [$pretController, 'afficherListeInteretsParMoisAjax']);

Flight::start();