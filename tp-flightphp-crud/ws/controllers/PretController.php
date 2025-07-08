<?php
require 'vendor/autoload.php';
require_once 'models/Pret.php';
require_once 'models/Client.php';
require_once 'models/TypePret.php';
require_once 'models/generate_pdf.php'; // Nouveau service PDF
require_once 'controllers/AuthController.php';

class PretController
{
    private $pretModel;
    private $clientModel;
    private $typePretModel;
    private $pdfGenerator;
    private $authController;

    public function __construct()
    {
        $this->pretModel = new Pret();
        $this->clientModel = new Client();
        $this->typePretModel = new TypePret();
        $this->pdfGenerator = new PDFGenerator(); // Initialisation du générateur PDF
        $this->authController = new AuthController(); // Initialisation du contrôleur d'authentification
    }

    public function listePrets()
    {
        $this->authController->verifierRole('admin'); // Vérification du rôle admin
        $prets = $this->pretModel->findAll();
        $clients = $this->clientModel->findAll();

        Flight::render('admin/template/template', [
            'page' => 'listePrets',
            'prets' => $prets,
            'clients' => $clients
        ]);
    }

    // Ajout de la méthode pour générer le PDF
    public function genererPDF($pretId)
    {
        try {
            if (!is_numeric($pretId)) {
                throw new Exception("ID de prêt invalide");
            }

            $pretDetails = $this->pretModel->getPretDetailsForPDF($pretId);

            $requiredFields = [
                'id',
                'client_nom',
                'type_pret',
                'montant',
                'statut',
                'date_demande',
                'duree_mois',
                'taux_interet'
            ];

            foreach ($requiredFields as $field) {
                if (!isset($pretDetails[$field])) {
                    throw new Exception("Champ requis manquant: $field");
                }
            }

            $pdfGenerator = new PDFGenerator();
            $pdfGenerator->generatePretPDF($pretDetails);
        } catch (PDOException $e) {
            $errorMsg = "Erreur base de données: " . $e->getMessage();
            error_log($errorMsg);
            Flight::redirect('/admin/prets?error=' . urlencode($errorMsg));
        } catch (Exception $e) {
            $errorMsg = "Erreur génération PDF: " . $e->getMessage();
            error_log($errorMsg);
            Flight::redirect('/admin/prets?error=' . urlencode($errorMsg));
        }
    }

    public function demandePret()
    {

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
            try {
                $pretId = $this->pretModel->insererPret($clientId, $montant, $typePretId, $dateDebut, $duree, $tauxAssurance, $delaiPremierRemboursement);
                Flight::json([
                    'success' => true,
                    'message' => 'Prêt ajouté avec succès',
                    'pret_id' => $pretId
                ]);
            } catch (Exception $e) {
                Flight::json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            Flight::json([
                'success' => false,
                'message' => 'Données invalides ou incomplètes'
            ]);
        }
    }

    public function approuverPret($pretId)
    {
        $result = $this->pretModel->validerPret($pretId);
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

    public function validerPret()
    {
        $this->authController->verifierRole('admin');
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
            $result = $this->pretModel->validerPret($pretId);
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

    public function rejeterPret()
    {
        $this->authController->verifierRole('admin');
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
            $result = $this->pretModel->rejeterPret($pretId);
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
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage()
            ]);
        }
    }

    public function afficherPretByUser($id)
    {
        $page = 'liste-prets';
        $prets = $this->pretModel->findByClientId($id);
        Flight::render('client/template/template', [
            'page' => $page,
            'prets' => $prets
        ]);
    }

    public function afficherFormPret()
    {

        $page = 'prets';
        $typesPret = $this->typePretModel->findAll();
        Flight::render('client/template/template', [
            'page' => $page,
            'typesPret' => $typesPret
        ]);
    }

    public function afficherListeInteretsParMois()
    {
        $this->authController->verifierRole('admin');
        $dateDebut = Flight::request()->query['date_debut'] ?? null;
        $dateFin = Flight::request()->query['date_fin'] ?? null;

        if ($dateDebut && $dateFin) {
            $interets = $this->pretModel->InteretsParMoisAnnee($dateDebut, $dateFin);
        } else {
            $interets = $this->pretModel->InteretsParMois();
        }

        Flight::render('admin/template/template', [
            'page' => 'tableauInterets',
            'interets' => $interets
        ]);
    }

    public function afficherListeInteretsParMoisAjax()
    {
        $this->authController->verifierRole('admin');
        $dateDebut = Flight::request()->query['date_debut'] ?? null;
        $dateFin = Flight::request()->query['date_fin'] ?? null;

        if ($dateDebut && $dateFin) {
            $interets = $this->pretModel->InteretsParMoisAnnee($dateDebut, $dateFin);
        } else {
            $interets = $this->pretModel->InteretsParMois();
        }

        // Retourner les données en JSON
        Flight::json([
            'success' => true,
            'data' => $interets
        ]);
    }

    public function getPret($pretId)
    {
        $pret = $this->pretModel->findById($pretId);
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

    public function afficherSimulationPret()
    {
        $pages = 'simulationPret';
        Flight::render('admin/template/template', [
            'page' => $pages
        ]);
    }

    public function simulerPret()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $montant = $input['montant'] ?? null;
        $duree = $input['duree'] ?? null;
        $tauxInteret = $input['taux_interet'] ?? null;
        $tauxAssurance = $input['taux_assurance'] ?? 0;

        if ($montant > 0 && $duree > 0 && $tauxInteret >= 0 && $tauxAssurance >= 0) {
            $amortissement = $this->pretModel->calculerAmortissement($montant, $tauxInteret, $duree, $tauxAssurance, 0);
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
    public function getDetailsPret($pretId = null)
    {
        if (!$pretId) {
            $pretId = Flight::request()->query['pret_id'] ?? null;
        }

        if (!$pretId) {
            Flight::json([
                'success' => false,
                'message' => 'ID du prêt manquant'
            ]);
            return;
        }

        $pretDetails = $this->pretModel->detailPret($pretId);
        Flight::render('client/template/template', [
            'page' => 'detailsPret',
            'pretDetails' => $pretDetails
        ]);
    }
}
