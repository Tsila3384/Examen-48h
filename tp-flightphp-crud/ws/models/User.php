<?php
// ws/models/User.php
class User {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function create($username, $email, $password, $role = 'client') {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([$username, $email, $hashedPassword, $role]);
    }
    public function findByUsernameOrEmail($usernameOrEmail) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
}
