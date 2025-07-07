<?php
require_once __DIR__ . '/../db.php';

class User {
    protected $table = 'users';
    protected $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function ajouterFonds($montant, $dateAjout) {
        $stmt = $this->db->prepare("INSERT INTO historique_fonds (id_etablissement, montant, id_type_operation, date_operation) VALUES (1, ?, 1, ?)");
        $stmt1 = $this->db->prepare("UPDATE etablissement SET fonds_disponibles = fonds_disponibles + ? WHERE id = 1");
        $stmt1->execute([$montant]);
        $stmt->execute([$montant, $dateAjout]);
        return $stmt->rowCount() > 0;
    }
}
