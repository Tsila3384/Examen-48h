<?php
require_once 'models/Taux.php';
class TauxController {
    private $tauxModel;
    public function __construct() {
        $this->tauxModel = new Taux();
    }

    public function insererTaux() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $type_client_id = $input['type_client_id'] ?? null;
        $type_pret_id = $input['type_pret_id'] ?? null;
        $taux_interet = $input['taux_interet'] ?? null;
        
        if (!$type_client_id || !$type_pret_id || !$taux_interet) {
            Flight::json([
                'success' => false,
                'message' => 'Données manquantes'
            ]);
            return;
        }
        
        $result = $this->tauxModel->insertTaux($type_client_id, $type_pret_id, $taux_interet);
        if ($result) {
            Flight::json([
                'success' => true,
                'message' => 'Taux inséré avec succès'
            ]);
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Erreur lors de l\'insertion du taux'
            ]);
        }
    }

    public function modifierTaux() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $id = $input['id'] ?? null;
        $type_client_id = $input['type_client_id'] ?? null;
        $type_pret_id = $input['type_pret_id'] ?? null;
        $taux_interet = $input['taux_interet'] ?? null;
        
        if (!$id || !$type_client_id || !$type_pret_id || !$taux_interet) {
            Flight::json([
                'success' => false,
                'message' => 'Données manquantes'
            ]);
            return;
        }
        
        $result = $this->tauxModel->ModifierTaux($id, $type_client_id, $type_pret_id, $taux_interet);
        if ($result) {
            Flight::json([
                'success' => true,
                'message' => 'Taux modifié avec succès'
            ]);
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Erreur lors de la modification du taux'
            ]);
        }
    }

    public function supprimerTaux() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $id = $input['id'] ?? null;
        
        if (!$id) {
            Flight::json([
                'success' => false,
                'message' => 'ID manquant'
            ]);
            return;
        }
        
        $result = $this->tauxModel->deleteTaux($id);
        if ($result) {
            Flight::json([
                'success' => true,
                'message' => 'Taux supprimé avec succès'
            ]);
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du taux'
            ]);
        }
    }

    public function listerTaux() {
        $taux = $this->tauxModel->findAll();
        Flight::json([
            'success' => true,
            'data' => $taux
        ]);
    }
    
    public function afficherListeTaux() {
        $typesClient = $this->tauxModel->getTypesClient();
        $typesPret = $this->tauxModel->getTypesPret();
        
        Flight::render('admin/template/template', [
            'page' => 'gestionTaux',
            'typesClient' => $typesClient,
            'typesPret' => $typesPret
        ]);
    }
}