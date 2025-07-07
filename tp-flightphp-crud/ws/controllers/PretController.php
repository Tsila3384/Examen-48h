<?php

class PretController {
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
            Flight::json($prets);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des prêts: ' . $e->getMessage()], 500);
        }
    }

    public function show($id) {
        try {
            $pret = $this->pretModel->findByIdWithDetails($id);
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }
            Flight::json($pret);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération du prêt: ' . $e->getMessage()], 500);
        }
    }

    public function store() {
        try {
            $data = Flight::request()->data->getData();
            $required = ['client_id', 'type_pret_id', 'montant', 'duree_mois'];
            $missing = array_filter($required, function($field) use ($data) {
                return !isset($data[$field]) || empty($data[$field]);
            });
            if (!empty($missing)) {
                Flight::json(['error' => 'Champs manquants: ' . implode(', ', $missing)]);
                return;
            }
            $client = $this->clientModel->findById($data['client_id']);
            if (!$client) {
                Flight::json(['error' => 'Client non trouvé']);
                return;
            }
            $typePret = $this->typePretModel->findById($data['type_pret_id']);
            if (!$typePret) {
                Flight::json(['error' => 'Type de prêt non trouvé']);
                return;
            }
            if ($data['duree_mois'] > $typePret['duree_max']) {
                Flight::json(['error' => 'Durée demandée supérieure à la durée maximale autorisée (' . $typePret['duree_max'] . ' mois)']);
                return;
            }
            $taux = $this->typeClientModel->getTauxForPret($client['type_client_id'], $data['type_pret_id']);
            if (!$taux) {
                Flight::json(['error' => 'Taux d\'intérêt non défini pour ce type de client et de prêt']);
                return;
            }
            $mensualite = $this->pretModel->calculerMensualite($data['montant'], $taux, $data['duree_mois']);
            $pretData = [
                'id_etablissement' => 1,
                'client_id' => $data['client_id'],
                'type_pret_id' => $data['type_pret_id'],
                'montant' => $data['montant'],
                'id_statut' => 1,
                'date_demande' => date('Y-m-d'),
                'mensualite' => round($mensualite, 2),
                'duree_mois' => $data['duree_mois']
            ];
            $id = $this->pretModel->create($pretData);
            Flight::json([
                'message' => 'Demande de prêt créée avec succès',
                'id' => $id,
                'mensualite' => round($mensualite, 2),
                'taux' => $taux
            ]);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la création du prêt: ' . $e->getMessage()], 500);
        }
    }

    public function update($id) {
        try {
            $data = Flight::request()->data->getData();
            $pret = $this->pretModel->findById($id);
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }
            $this->pretModel->update($id, $data);
            Flight::json(['message' => 'Prêt mis à jour avec succès']);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la mise à jour du prêt: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        try {
            $pret = $this->pretModel->findById($id);
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }
            $this->pretModel->delete($id);
            Flight::json(['message' => 'Prêt supprimé avec succès']);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la suppression du prêt: ' . $e->getMessage()], 500);
        }
    }

    public function approve($id) {
        try {
            $pret = $this->pretModel->findById($id);
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }
            $this->pretModel->update($id, ['id_statut' => 2]);
            Flight::json(['message' => 'Prêt approuvé avec succès']);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de l\'approbation du prêt: ' . $e->getMessage()], 500);
        }
    }

    public function reject($id) {
        try {
            $pret = $this->pretModel->findById($id);
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }
            $this->pretModel->update($id, ['id_statut' => 3]);
            Flight::json(['message' => 'Prêt rejeté avec succès']);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors du rejet du prêt: ' . $e->getMessage()], 500);
        }
    }
}
