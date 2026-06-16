<?php

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Ellenorzi a felhasznalonevet es jelszot a users tablaban.
    // Onmagaban nem modositja a sessiont.
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
            unset($user['password']);
            return $user;
        }

        return null;
    }

    // Az admin/index.php hivja meg, amikor elkuldik a belepesi urlapot.
    // Siker eseten sessionbe menti a felhasznalo id-jet, nevet es szerepkoret.
    public function login($username, $password)
    {
        $user = $this->authenticate($username, $password);

        if (!$user) {
            return false;
        }

        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('role', $user['role']);

        return true;
    }

    // A jelenlegi verzioban az admin/index.php?action=logout hivja meg.
    // A kijelentkezes az admin index route-on keresztul tortenik, nem kulon fajlban.
    public static function logout()
    {
        Session::destroy();
    }

    // Publikus felhasznaloi adatokat ad vissza id alapjan.
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
