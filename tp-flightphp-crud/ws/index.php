<?php
require 'vendor/autoload.php';
require 'db.php';
require 'controllers/UserController.php';

$base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', rtrim($base_url, '/'));

Flight::route('GET /etudiants', function() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM etudiant");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

$userController = new UserController();

Flight::route('POST /user/ajouterFond', [$userController, 'ajouterFonds']);
Flight::route('GET /user/formulaireFond', [$userController, 'formulaireAjoutFonds']);

Flight::start();