<?php
class TypePret
{
    protected $db;
    protected $table = 'type_pret';

    public function __construct()
    {
        $this->db = getDB();
    }

    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

    