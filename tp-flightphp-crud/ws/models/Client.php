<?php
class Client {
    protected $table = 'clients';
    protected $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function findAll(){
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserId($id) {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE user_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findClientByUserId($id) {
        $stmt = $this->db->prepare("SELECT id FROM clients WHERE user_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null; // Retourne l'ID ou null
    }
      
    public function findById($id)
    {
        try {
            $sql = "SELECT * FROM clients WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur findById Client: " . $e->getMessage());
            return false;
        }
    }
}