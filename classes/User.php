<?php
class User {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function authenticate(string $username, string $password): ?array {
        $stmt = $this->db->prepare("SELECT id, username, email, password, role FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        
        return null;
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, username, email, role FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}