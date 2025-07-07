<?php

class ClientController extends BaseController {
    private $clientModel;
    private $typeClientModel;

    public function __construct() {
        $this->clientModel = new Client();
        $this->typeClientModel = new TypeClient();
    }

    public function index() {
        try {
            $clients = $this->clientModel->findWithTypeClient();
            $this->jsonResponse($clients);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des clients: ' . $e->getMessage(), 500);
        }
    }

    public function show($id) {
        try {
            $client = $this->clientModel->findById($id);
            if (!$client) {
                $this->errorResponse('Client non trouvé', 404);
                return;
            }
            $this->jsonResponse($client);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la récupération du client: ' . $e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = Flight::request()->data->getData();
            
            $required = ['nom', 'email', 'salaire', 'user_id', 'type_client_id'];
            $missing = $this->validateRequired($data, $required);
            
            if (!empty($missing)) {
                $this->errorResponse('Champs manquants: ' . implode(', ', $missing));
                return;
            }

            // Vérifier si l'email existe déjà
            if ($this->clientModel->findByEmail($data['email'])) {
                $this->errorResponse('Un client avec cet email existe déjà');
                return;
            }

            $id = $this->clientModel->create($data);
            $this->successResponse('Client créé avec succès', ['id' => $id]);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la création du client: ' . $e->getMessage(), 500);
        }
    }

    public function update($id) {
        try {
            $data = Flight::request()->data->getData();
            
            $client = $this->clientModel->findById($id);
            if (!$client) {
                $this->errorResponse('Client non trouvé', 404);
                return;
            }

            // Vérifier si l'email existe déjà (sauf pour le client actuel)
            if (isset($data['email'])) {
                $existingClient = $this->clientModel->findByEmail($data['email']);
                if ($existingClient && $existingClient['id'] != $id) {
                    $this->errorResponse('Un autre client avec cet email existe déjà');
                    return;
                }
            }

            $this->clientModel->update($id, $data);
            $this->successResponse('Client mis à jour avec succès');
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la mise à jour du client: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id) {
        try {
            $client = $this->clientModel->findById($id);
            if (!$client) {
                $this->errorResponse('Client non trouvé', 404);
                return;
            }

            $this->clientModel->delete($id);
            $this->successResponse('Client supprimé avec succès');
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la suppression du client: ' . $e->getMessage(), 500);
        }
    }

    public function getPrets($clientId) {
        try {
            $prets = $this->clientModel->getClientsPrets($clientId);
            $this->jsonResponse($prets);
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des prêts: ' . $e->getMessage(), 500);
        }
    }
}
