<?php
require 'vendor/autoload.php';
require 'db.php';
require_once __DIR__ . '/controllers/AuthController.php';

session_start();
$authController = new AuthController($db);

// Redirection automatique vers /login si l'utilisateur n'est pas connecté et n'est pas déjà sur une page d'auth
$publicRoutes = ['/login', '/inscription'];
$currentUri = strtok($_SERVER['REQUEST_URI'], '?');
if (!isset($_SESSION['user']) && !in_array($currentUri, $publicRoutes)) {
    header('Location: /login');
    exit;
}

// Auth routes
Flight::route('GET /inscription', function() {
    include __DIR__ . '/views/auth/register.php';
});

Flight::route('POST /inscription', function() use ($authController) {
    $data = Flight::request()->data;
    $result = $authController->register((array)$data);
    if ($result['success']) {
        Flight::redirect('/login?register=success');
    } else {
        $error = $result['message'];
        include __DIR__ . '/views/auth/register.php';
    }
});

Flight::route('GET /login', function() {
    include __DIR__ . '/views/auth/login.php';
});

Flight::route('POST /login', function() use ($authController) {
    $data = Flight::request()->data;
    $result = $authController->login($data['username'], $data['password']);
    if ($result['success']) {
        if ($result['role'] === 'admin') {
            Flight::redirect('/admin');
        } else {
            Flight::redirect('/client');
        }
    } else {
        $error = $result['message'];
        include __DIR__ . '/views/auth/login.php';
    }
});

Flight::route('GET /client', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
        Flight::redirect('/login');
        return;
    }
    include __DIR__ . '/views/client.php';
});

Flight::route('GET /admin', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        Flight::redirect('/login');
        return;
    }
    include __DIR__ . '/views/admin.php';
});

Flight::route('GET /logout', function() {
    session_destroy();
    Flight::redirect('/login');
});

Flight::route('GET /etudiants', function() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM etudiant");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

Flight::route('GET /etudiants/@id', function($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM etudiant WHERE id = ?");
    $stmt->execute([$id]);
    Flight::json($stmt->fetch(PDO::FETCH_ASSOC));
});

Flight::route('POST /etudiants', function() {
    $data = Flight::request()->data;
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO etudiant (nom, prenom, email, age) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age]);
    Flight::json(['message' => 'Étudiant ajouté', 'id' => $db->lastInsertId()]);
});

Flight::route('PUT /etudiants/@id', function($id) {
    parse_str(file_get_contents('php://input'), result: $data);
    if (empty($data)) { die('Aucune donnée reçue'); }
    $db = getDB();
    $stmt = $db->prepare("UPDATE etudiant SET nom = ?, prenom = ?, email = ?, age = ? WHERE id = ?");
    $stmt->execute([$data['nom'], $data['prenom'], $data['email'], $data['age'], $id]) or die(print_r($stmt->errorInfo(), true));
    Flight::json(['message' => 'Étudiant modifié']);
});


Flight::route('DELETE /etudiants/@id', function($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM etudiant WHERE id = ?");
    $stmt->execute([$id]);
    Flight::json(['message' => 'Étudiant supprimé']);
});

Flight::start();