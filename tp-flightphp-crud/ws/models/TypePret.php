<?php
require_once __DIR__ . '/../db.php';

class TypePret
{
    protected $table = 'type_pret';
    protected $db;

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

    
    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (nom, duree_max) VALUES (:nom, :duree_max)");
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':duree_max' => $data['duree_max']
        ]);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET nom = :nom, duree_max = :duree_max WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $data['nom'],
            ':duree_max' => $data['duree_max']
        ]);
    }

    public function delete($id)
    {
        $this->db->beginTransaction();
        try {
            // Supprimer les taux associés
            $this->db->prepare("DELETE FROM taux WHERE type_pret_id = ?")->execute([$id]);
            // Supprimer le type
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $result = $stmt->execute([$id]);
            $this->db->commit();

            // Redirection après suppression réussie
            if ($result && isset($_POST['redirect_url'])) {
                header('Location: ' . $_POST['redirect_url']);
                exit;
            }
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }


    public function findAllByUser($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT tp.id, tp.nom, tp.duree_max, t.taux_interet
            FROM type_pret tp
            JOIN taux t ON tp.id = t.type_pret_id
            JOIN clients c ON t.type_client_id = c.type_client_id
            WHERE c.id = ?
            ORDER BY tp.nom
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function setTaux($type_pret_id, $type_client_id, $taux_interet)
    {
        if (
            $this->db->prepare("SELECT id FROM taux WHERE type_pret_id = ? AND type_client_id = ?")
                ->execute([$type_pret_id, $type_client_id])->fetch()
        ) {
            $stmt = $this->db->prepare("UPDATE taux SET taux_interet = ? WHERE type_pret_id = ? AND type_client_id = ?");
        } else {
            $stmt = $this->db->prepare("INSERT INTO taux (type_pret_id, type_client_id, taux_interet) VALUES (?, ?, ?)");
        }
        return $stmt->execute([$taux_interet, $type_pret_id, $type_client_id]);
    }

    public function findAllWithTaux()
{
    $stmt = $this->db->prepare("
        SELECT tp.id, tp.nom, tp.duree_max, t.type_client_id, t.taux_interet
        FROM {$this->table} tp
        LEFT JOIN taux t ON tp.id = t.type_pret_id
        ORDER BY tp.nom, t.type_client_id
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
