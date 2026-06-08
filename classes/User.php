<?php

// A User osztály a felhasználók adatbázisból történő lekéréséért és beléptetéséért felel.
class User
{
    // PDO kapcsolat, amelyet a Database singleton ad.
    private $db;

    // Konstruktor: elkéri az adatbázis-kapcsolatot.
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Bejelentkezés ellenőrzése.
    // Az SQL a felhasználónevet keresi, a jelszót pedig password_verify() ellenőrzi a hash alapján.
    public function authenticate($username, $password)
    {
        $stmt = $this->db->prepare("
            SELECT id, username, email, password, role
            FROM users
            WHERE username = :username
        ");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // A jelszó hash-t nem adjuk tovább, mert a felületnek nincs rá szüksége.
            unset($user['password']);
            return $user;
        }

        return null;
    }

    // Felhasználó lekérése azonosító alapján.
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT id, username, email, role
            FROM users
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }
}
