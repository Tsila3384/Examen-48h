<?php

class ClientController {
    private $clientModel;
    private $typeClientModel;

    public function __construct() {
        $this->clientModel = new Client();
        $this->typeClientModel = new TypeClient();
    }

    public function index() {
        try {
            $clients = $this->clientModel->findWithTypeClient();
            Flight::json($clients);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des clients: ' . $e->getMessage()], 500);
        }
    }

    public function show($id) {
        try {
            $client = $this->clientModel->findById($id);
            if (!$client) {
                Flight::json(['error' => 'Client non trouvé'], 404);
                return;
            }
            Flight::json($client);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération du client: ' . $e->getMessage()], 500);
        }
    }

    public function store() {
        try {
            $data = Flight::request()->data->getData();
            
            $required = ['nom', 'email', 'salaire', 'user_id', 'type_client_id'];
            $missing = array_filter($required, function($field) use ($data) {
                return !isset($data[$field]) || empty($data[$field]);
            });
            
            if (!empty($missing)) {
                Flight::json(['error' => 'Champs manquants: ' . implode(', ', $missing)]);
                return;
            }

            if ($this->clientModel->findByEmail($data['email'])) {
                Flight::json(['error' => 'Un client avec cet email existe déjà']);
                return;
            }

            $id = $this->clientModel->create($data);
            Flight::json(['message' => 'Client créé avec succès', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la création du client: ' . $e->getMessage()], 500);
        }
    }

    public function update($id) {
        try {
            $data = Flight::request()->data->getData();
            
            $client = $this->clientModel->findById($id);
            if (!$client) {
                Flight::json(['error' => 'Client non trouvé'], 404);
                return;
            }

            if (isset($data['email'])) {
                $existingClient = $this->clientModel->findByEmail($data['email']);
                if ($existingClient && $existingClient['id'] != $id) {
                    Flight::json(['error' => 'Un autre client avec cet email existe déjà']);
                    return;
                }
            }

            $this->clientModel->update($id, $data);
            Flight::json(['message' => 'Client mis à jour avec succès']);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la mise à jour du client: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        try {
            $client = $this->clientModel->findById($id);
            if (!$client) {
                Flight::json(['error' => 'Client non trouvé'], 404);
                return;
            }

            $this->clientModel->delete($id);
            Flight::json(['message' => 'Client supprimé avec succès']);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la suppression du client: ' . $e->getMessage()], 500);
        }
    }

    public function getPrets($clientId) {
        try {
            $prets = $this->clientModel->getClientsPrets($clientId);
            Flight::json($prets);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des prêts: ' . $e->getMessage()], 500);
        }
    }
}
