<?php

require 'vendor/autoload.php';
require_once 'models/Pret.php';
require_once 'models/Client.php';

class PretController {
    private $PretModel;
    private $clientModel;
    public function __construct() {
        $this->PretModel = new Pret();
        $this->clientModel = new Client();

    }

    public function listePrets() {
        $prets = $this->PretModel->findAll();
        $clients = $this->clientModel->findAll();
        Flight::render('admin/template/template', [
            'page' => 'listePrets',
            'prets' => $prets,
            'clients' => $clients
        ]);
    }

    public function validationPret() {
        $clientId = $_POST['client_id'] ?? null;
        $montant = $_POST['montant'] ?? null;
        $typePretId = $_POST['type_pret_id'] ?? null;
        $dateDebut = $_POST['date_debut'] ?? null;
        $duree = $_POST['duree'] ?? null;
        if ($clientId && $montant && $typePretId && $dateDebut && $duree) {
            $this->PretModel->insererPret($clientId, $montant, $typePretId, $dateDebut, $duree);
            Flight::json([
                'success' => true,
                'message' => 'Prêt ajouté avec succès'
            ]);
        }
    }

    public function approuverPret($pretId) {
        $result = $this->PretModel->validerPret($pretId);
        if ($result) {
            Flight::json([
                'success' => true,
                'message' => 'Prêt approuvé avec succès'
            ]);
        }
        else {
            Flight::json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation du prêt'
            ]);
        }
    }

    public function validerPret() {
        // Récupérer les données de la requête
        $input = json_decode(file_get_contents('php://input'), true);
        $pretId = $input['pret_id'] ?? null;

        if (!$pretId) {
            Flight::json([
                'success' => false,
                'message' => 'ID du prêt manquant'
            ]);
            return;
        }

        try {
            $result = $this->PretModel->validerPret($pretId);
            if ($result) {
                Flight::json([
                    'success' => true,
                    'message' => 'Prêt validé avec succès'
                ]);
            } else {
                Flight::json([
                    'success' => false,
                    'message' => 'Erreur lors de la validation du prêt'
                ]);
            }
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage()
            ]);
        }
    }

    public function rejeterPret($pretId){
        $result = $this->PretModel->rejeterPret($pretId);
        if ($result) {
            Flight::json([
                'success' => true,
                'message' => 'Prêt rejeté avec succès'
            ]);
        }
        else {
            Flight::json([
                'success' => false,
                'message' => 'Erreur lors du rejet du prêt'
            ]);
        }
    }
}