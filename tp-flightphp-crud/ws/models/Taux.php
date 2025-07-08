<?php 
class Taux {
    protected $table = 'taux';
    protected $db;
    public function __construct() {
        $this->db = getDB();
    }
    public function findAll() {
        $stmt = $this->db->prepare("
            SELECT t.*, 
                   tc.libelle as type_client_nom, 
                   tp.nom as type_pret_nom 
            FROM {$this->table} t
            LEFT JOIN type_client tc ON t.type_client_id = tc.id
            LEFT JOIN type_pret tp ON t.type_pret_id = tp.id
            ORDER BY tc.libelle, tp.nom
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertTaux($typeClientId, $typePretId, $tauxInteret) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (type_client_id, type_pret_id, taux_interet) VALUES (:type_client_id, :type_pret_id, :taux_interet)");
        return $stmt->execute([
            ':type_client_id' => $typeClientId,
            ':type_pret_id' => $typePretId,
            ':taux_interet' => $tauxInteret
        ]);
    }

    public function ModifierTaux($id, $typeClientId, $typePretId, $tauxInteret) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET type_client_id = :type_client_id, type_pret_id = :type_pret_id, taux_interet = :taux_interet WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':type_client_id' => $typeClientId,
            ':type_pret_id' => $typePretId,
            ':taux_interet' => $tauxInteret
        ]);
    }

    public function deleteTaux($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTypesClient() {
        $stmt = $this->db->prepare("SELECT id, libelle FROM type_client ORDER BY libelle");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTypesPret() {
        $stmt = $this->db->prepare("SELECT id, nom FROM type_pret ORDER BY nom");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}