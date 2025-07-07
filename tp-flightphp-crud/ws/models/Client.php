<?php
// ws/models/Client.php
class Client {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function create($nom, $email, $salaire, $user_id, $type_client_id) {
        $stmt = $this->db->prepare("INSERT INTO clients (nom, email, salaire, user_id, type_client_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$nom, $email, $salaire, $user_id, $type_client_id]);
    }
}
