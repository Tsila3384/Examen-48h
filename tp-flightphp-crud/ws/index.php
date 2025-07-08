<?php
require 'vendor/autoload.php';
require 'db.php';
require 'controllers/AuthController.php';
require 'controllers/TypePretController.php';
require 'controllers/TauxController.php';

session_start();
require_once('controllers/UserController.php');
require_once('controllers/PretController.php');
require_once('models/Client.php');

// Initialisation des contrôleurs
$authController = new AuthController();
$typePretController = new TypePretController();
$userController = new UserController();
$pretController = new PretController();
$tauxController = new TauxController();

// Configuration de l'URL de base
$base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($base_url, '/');
define('BASE_URL', $base_url === '' ? '' : $base_url);

// Route par défaut
Flight::route('GET /', function() {
    if (isset($_SESSION['user'])) {
        $redirect = $_SESSION['user']['role'] === 'admin' ? '/admin/dashboard' : '/client/dashboard';
        Flight::redirect($redirect);
    } else {
        Flight::redirect('/auth/connexion');
    }
});

// Routes d'authentification (publiques)
Flight::route('GET /auth/connexion', [$authController, 'afficherConnexion']);
Flight::route('GET /auth/inscription', [$authController, 'afficherInscription']);
Flight::route('POST /auth/connexion', [$authController, 'connexion']);
Flight::route('POST /auth/inscription', [$authController, 'inscription']);
Flight::route('GET /auth/deconnexion', [$authController, 'deconnexion']);
Flight::route('POST /auth/deconnexion', [$authController, 'deco']);

// Routes Client
Flight::route('GET /client/dashboard', function() use ($authController) {
    $authController->verifierRole('client');
    Flight::render('client/template/template', ['page' => 'dashboard']);
});

Flight::route('GET /client/types-pret', function() use ($typePretController) {
    $typePretController->getTypesByUser($_SESSION['id']);
});

Flight::route('GET /client/prets/formulairePret', [$pretController, 'afficherFormPret']);
Flight::route('POST /client/pret/demandePret', [$pretController, 'demandePret']);
Flight::route('GET /client/pret/simuler', [$pretController, 'afficherSimulationPret']);
Flight::route('POST /client/pret/simuler', [$pretController, 'simulerPret']);

// Routes prêts côté client
Flight::route('GET /user/listePret', function() use ($pretController) {
    $clientModel = new Client();
    $clientId = $clientModel->findClientByUserId($_SESSION['user_id']);
    if ($clientId) {
        $pretController->afficherPretByUser($clientId);
    } else {
        Flight::json(['error' => 'Client introuvable']);
    }
});
Flight::route('GET /user/pret/details/@id', function($id) use ($pretController) {
    $pretController->getDetailsPret($id);
});
Flight::route('/user/prets/pdf/@id', function($id) use ($pretController) {
    $pretController->genererPDF($id);
});

// Routes Admin - Dashboard
Flight::route('GET /admin/dashboard', function() use ($authController, $userController) {
    $authController->verifierRole('admin');
    $userController->dashboard();
});

// Routes Admin - Gestion des clients
Flight::route('GET /admin/clients/nouveau', function() use ($authController) {
    $authController->verifierRole('admin');
    Flight::render('admin/template/template', ['page' => 'insertClient']);
});
Flight::route('POST /admin/clients/nouveau', [$authController, 'inscription']);

// Routes Admin - Gestion des fonds
Flight::route('POST /user/ajouterFond', function() use ($userController, $authController) {
    $authController->verifierRole('admin');
    $userController->ajouterFonds();
});
Flight::route('GET /user/formulaireFond', function() use ($userController, $authController) {
    $authController->verifierRole('admin');
    $userController->formulaireAjoutFonds();
});

// Routes Admin - Gestion des prêts
Flight::route('GET /admin/prets', [$pretController, 'listePrets']);
Flight::route('GET /admin/prets/nouveau', [$pretController, 'afficherFormDemandePretAdmin']);
Flight::route('POST /admin/prets/nouveau', [$pretController, 'demandePret']);
Flight::route('GET /admin/prets/details/@id', [$pretController, 'getDetailsPret']);
Flight::route('GET /admin/prets/pdf/@id', [$pretController, 'genererPDF']);

// Routes Admin - Actions sur les prêts
Flight::route('POST /pret/approuverPret', [$pretController, 'approuverPret']);
Flight::route('POST /pret/valider', [$pretController, 'validerPret']);
Flight::route('POST /pret/rejeter', [$pretController, 'rejeterPret']);

// Routes Admin - Types de prêt
Flight::route('GET /admin/types-pret', [$typePretController, 'getAllTypes']);
Flight::route('GET /admin/types-pret/create', [$typePretController, 'create']);
Flight::route('POST /admin/types-pret', [$typePretController, 'store']);
Flight::route('GET /admin/types-pret/edit/@id', [$typePretController, 'edit']);
Flight::route('POST /admin/types-pret/update/@id', [$typePretController, 'update']);
Flight::route('POST /admin/types-pret/delete/@id', [$typePretController, 'destroy']);

// Routes Admin - Gestion des taux
Flight::route('GET /admin/taux', [$tauxController, 'afficherListeTaux']);
Flight::route('GET /admin/taux/ajax', [$tauxController, 'listerTaux']);
Flight::route('POST /admin/taux/inserer', [$tauxController, 'insererTaux']);
Flight::route('POST /admin/taux/modifier', [$tauxController, 'modifierTaux']);
Flight::route('POST /admin/taux/supprimer', [$tauxController, 'supprimerTaux']);

// Routes Admin - Rapports et intérêts
Flight::route('GET /admin/interets', [$pretController, 'afficherListeInteretsParMois']);
Flight::route('GET /admin/interets/ajax', [$pretController, 'afficherListeInteretsParMoisAjax']);

// Routes Admin - Tableau des fonds
Flight::route('GET /admin/fonds', [$pretController, 'afficherFondsDisponibles']);
Flight::route('GET /admin/fonds/ajax', [$pretController, 'afficherFondsDisponiblesAjax']);

Flight::start();
