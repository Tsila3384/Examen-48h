<?php

use flight\net\Response;

require 'vendor/autoload.php';
require_once 'models/User.php';

class UserController {
    private $userModel;
    public function __construct() {
        $this->userModel = new User();
    }

    public function formulaireAjoutFonds() {
        $page = 'ajouterFond';
        Flight::render('admin/template/template', [
            'page' => $page,
        ]);
    }


    public function ajouterFonds() {
        $montant = $_POST['montant'] ?? null;
        $dateAjout = $_POST['dateAjout'] ?? null;
        if ($montant && $dateAjout) {
            $result = $this->userModel->ajouterFonds($montant, $dateAjout);
        }
        if ($result) {
            Flight::json([
                'success' => true,
                'message' => 'Fonds ajoutés avec succès'
            ]);
        }
        else {
            Flight::json([
                'success' => false,
                'message' => 'erreur lors de l\'ajout des fonds'
            ]); 
        }
    }
}
