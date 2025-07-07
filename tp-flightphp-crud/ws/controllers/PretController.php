<?php
require 'vendor/autoload.php';
require_once 'models/Pret.php';
require_once 'models/Client.php';
require_once 'models/TypePret.php';

class PretController {
    private $PretModel;
    private $clientModel;
    private $typePretModel;

    public function __construct() {
        $this->PretModel = new Pret();
        $this->clientModel = new Client();
        $this->typePretModel = new TypePret();
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

    public function demandePret() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $user_id = $_SESSION['user_id'] ?? null;
        
        if (!$user_id) {
            Flight::json([
                'success' => false,
                'message' => 'Utilisateur non connecté'
            ]);
            return;
        }
        
        $clientId = $this->clientModel->findClientByUserId($user_id);
        $montant = $input['montant'] ?? null;
        $typePretId = $input['type_pret_id'] ?? null;
        $dateDebut = $input['date_debut'] ?? null;
        $duree = $input['duree'] ?? null;
        $tauxAssurance = $input['taux_assurance'] ?? 0;
        $delaiPremierRemboursement = $input['delai_premier_remboursement'] ?? 0;

        if ($clientId && $montant > 0 && $typePretId && $dateDebut && $duree > 0 && $tauxAssurance >= 0 && $delaiPremierRemboursement >= 0) {
            $pretId = $this->PretModel->insererPret($clientId, $montant, $typePretId, $dateDebut, $duree, $tauxAssurance, $delaiPremierRemboursement);
            Flight::json([
                'success' => true,
                'message' => 'Prêt ajouté avec succès',
                'pret_id' => $pretId
            ]);
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Données invalides ou incomplètes'
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
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation du prêt'
            ]);
        }
    }

    public function validerPret() {
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

    public function rejeterPret($pretId) {
        $result = $this->PretModel->rejeterPret($pretId);
        if ($result) {
            Flight::json([
                'success' => true,
                'message' => 'Prêt rejeté avec succès'
            ]);
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Erreur lors du rejet du prêt'
            ]);
        }
    }

    public function afficherFormPret() {
        $page = 'prets';
        $typesPret = $this->typePretModel->findAll();
        Flight::render('client/template/template', [
            'page' => $page,
            'typesPret' => $typesPret
        ]);
    }

    public function getPret($pretId) {
        $pret = $this->PretModel->findById($pretId);
        if ($pret) {
            Flight::json([
                'success' => true,
                'data' => $pret
            ]);
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Prêt introuvable'
            ]);
        }
    }

    public function afficherSimulationPret() {
        $pages= 'simulationPret';
        Flight::render('client/template/template', [
            'page' => $pages
        ]);
    }

    public function simulerPret() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $montant = $input['montant'] ?? null;
        $duree = $input['duree'] ?? null;
        $tauxInteret = $input['taux_interet'] ?? null;
        $tauxAssurance = $input['taux_assurance'] ?? 0;

        if ($montant > 0 && $duree > 0 && $tauxInteret >= 0 && $tauxAssurance >= 0) {
            $amortissement = $this->PretModel->calculerAmortissement($montant, $tauxInteret, $duree, $tauxAssurance, 0);
            Flight::json([
                'success' => true,
                'data' => $amortissement
            ]);
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Données invalides ou incomplètes'
            ]);
        }
    }
}