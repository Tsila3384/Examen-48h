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
        // Récupérer les données JSON
        $input = json_decode(file_get_contents('php://input'), true);
            
        // Si pas de données JSON, utiliser POST
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
    public function afficherFormPret() {
        $page = 'prets';
        $typesPret = $this->typePretModel->findAll();
        Flight::render('client/template/template', [
            'page' => $page,
            'typesPret' => $typesPret
        ]);
    }

    public function afficherListeInteretsParMois() {
        $dateDebut = Flight::request()->query['date_debut'] ?? null;
        $dateFin = Flight::request()->query['date_fin'] ?? null;
        
        if ($dateDebut && $dateFin) {
            $interets = $this->PretModel->InteretsParMoisAnnee($dateDebut, $dateFin);
        } else {
            $interets = $this->PretModel->InteretsParMois();
        }
        
        Flight::render('admin/template/template', [
            'page' => 'tableauInterets',
            'interets' => $interets
        ]);
    }

    public function afficherListeInteretsParMoisAjax() {
        $dateDebut = Flight::request()->query['date_debut'] ?? null;
        $dateFin = Flight::request()->query['date_fin'] ?? null;
        
        if ($dateDebut && $dateFin) {
            $interets = $this->PretModel->InteretsParMoisAnnee($dateDebut, $dateFin);
        } else {
            $interets = $this->PretModel->InteretsParMois();
        }
        
        // Retourner les données en JSON
        Flight::json([
            'success' => true,
            'data' => $interets
        ]);
    }

}