<?php

class PretController extends BaseController {
    private $pretModel;
    private $clientModel;
    private $typePretModel;
    private $typeClientModel;

    public function __construct() {
        $this->pretModel = new Pret();
        $this->clientModel = new Client();
        $this->typePretModel = new TypePret();
        $this->typeClientModel = new TypeClient();
    }

    public function index() {
        try {
            $prets = $this->pretModel->findWithDetails();
            $this->jsonResponse($prets);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des prêts: ' . $e->getMessage(), 500);
        }
    }

    public function show($id) {
        try {
            $pret = $this->pretModel->findByIdWithDetails($id);
            if (!$pret) {
                $this->errorResponse('Prêt non trouvé', 404);
                return;
            }
            $this->jsonResponse($pret);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la récupération du prêt: ' . $e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = Flight::request()->data->getData();
            
            $required = ['client_id', 'type_pret_id', 'montant', 'duree_mois'];
            $missing = $this->validateRequired($data, $required);
            
            if (!empty($missing)) {
                $this->errorResponse('Champs manquants: ' . implode(', ', $missing));
                return;
            }

            // Vérifier que le client existe
            $client = $this->clientModel->findById($data['client_id']);
            if (!$client) {
                $this->errorResponse('Client non trouvé');
                return;
            }

            // Vérifier que le type de prêt existe
            $typePret = $this->typePretModel->findById($data['type_pret_id']);
            if (!$typePret) {
                $this->errorResponse('Type de prêt non trouvé');
                return;
            }

            // Vérifier la durée maximale
            if ($data['duree_mois'] > $typePret['duree_max']) {
                $this->errorResponse('Durée demandée supérieure à la durée maximale autorisée (' . $typePret['duree_max'] . ' mois)');
                return;
            }

            // Calculer le taux d'intérêt
            $taux = $this->typeClientModel->getTauxForPret($client['type_client_id'], $data['type_pret_id']);
            if (!$taux) {
                $this->errorResponse('Taux d\'intérêt non défini pour ce type de client et de prêt');
                return;
            }

            // Calculer la mensualité
            $mensualite = $this->pretModel->calculerMensualite($data['montant'], $taux, $data['duree_mois']);

            // Préparer les données du prêt
            $pretData = [
                'id_etablissement' => 1, // Supposons qu'il y a un seul établissement
                'client_id' => $data['client_id'],
                'type_pret_id' => $data['type_pret_id'],
                'montant' => $data['montant'],
                'id_statut' => 1, // En attente
                'date_demande' => date('Y-m-d'),
                'mensualite' => round($mensualite, 2),
                'duree_mois' => $data['duree_mois']
            ];

            $id = $this->pretModel->create($pretData);
            $this->successResponse('Demande de prêt créée avec succès', [
                'id' => $id,
                'mensualite' => round($mensualite, 2),
                'taux' => $taux
            ]);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la création du prêt: ' . $e->getMessage(), 500);
        }
    }

    public function update($id) {
        try {
            $data = Flight::request()->data->getData();
            
            $pret = $this->pretModel->findById($id);
            if (!$pret) {
                $this->errorResponse('Prêt non trouvé', 404);
                return;
            }

            $this->pretModel->update($id, $data);
            $this->successResponse('Prêt mis à jour avec succès');
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la mise à jour du prêt: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id) {
        try {
            $pret = $this->pretModel->findById($id);
            if (!$pret) {
                $this->errorResponse('Prêt non trouvé', 404);
                return;
            }

            $this->pretModel->delete($id);
            $this->successResponse('Prêt supprimé avec succès');
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la suppression du prêt: ' . $e->getMessage(), 500);
        }
    }

    public function approve($id) {
        try {
            $pret = $this->pretModel->findById($id);
            if (!$pret) {
                $this->errorResponse('Prêt non trouvé', 404);
                return;
            }

            $this->pretModel->update($id, ['id_statut' => 2]); // Approuvé
            $this->successResponse('Prêt approuvé avec succès');
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de l\'approbation du prêt: ' . $e->getMessage(), 500);
        }
    }

    public function reject($id) {
        try {
            $pret = $this->pretModel->findById($id);
            if (!$pret) {
                $this->errorResponse('Prêt non trouvé', 404);
                return;
            }

            $this->pretModel->update($id, ['id_statut' => 3]); // Rejeté
            $this->successResponse('Prêt rejeté avec succès');
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors du rejet du prêt: ' . $e->getMessage(), 500);
        }
    }
}
