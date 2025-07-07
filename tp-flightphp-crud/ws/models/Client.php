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
}