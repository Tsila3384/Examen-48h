<?php

class Client extends BaseModel {
    protected $table = 'clients';

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("SELECT c.*, tc.libelle as type_client 
                                   FROM clients c 
                                   JOIN type_client tc ON c.type_client_id = tc.id 
                                   WHERE c.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function findWithTypeClient() {
        $stmt = $this->db->prepare("SELECT c.*, tc.libelle as type_client 
                                   FROM clients c 
                                   JOIN type_client tc ON c.type_client_id = tc.id");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getClientsPrets($clientId) {
        $stmt = $this->db->prepare("SELECT p.*, tp.nom as type_pret, s.libelle as statut 
                                   FROM prets p 
                                   JOIN type_pret tp ON p.type_pret_id = tp.id 
                                   JOIN statut s ON p.id_statut = s.id 
                                   WHERE p.client_id = ?");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }
}
