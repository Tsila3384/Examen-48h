<?php
// Ce fichier est en conflit avec app/models/User.php. Supprimez ce fichier ou renommez la classe pour éviter le conflit.

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

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function createUser($data) {
        // Ne pas hasher le mot de passe (pour debug ou compatibilité)
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function verifyPassword($password, $hash) {
        // Comparaison directe pour mots de passe en clair (debug)
        return $password === $hash;
    }

    public function isActive($id) {
        $stmt = $this->db->prepare("SELECT is_active FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ? $result['is_active'] : false;
    }

    public function ajouterFonds($montant, $dateAjout) {
        $stmt = $this->db->prepare("INSERT INTO historique_fonds (id_etablissement, montant, id_type_operation, date_operation) VALUES (1, ?, 1, ?)");
        $stmt1 = $this->db->prepare("UPDATE etablissement SET fonds_disponibles = fonds_disponibles + ? WHERE id = 1");
        $stmt1->execute([$montant]);
        $stmt->execute([$montant, $dateAjout]);
        return $stmt->rowCount() > 0;
    }

    
}
