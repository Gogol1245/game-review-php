<?php

// A Session osztály a bejelentkezett felhasználó állapotát kezeli.
// Session nélkül minden oldalbetöltés külön kérés lenne, és a PHP nem tudná, ki van bejelentkezve.
class Session
{
    // Elindítja a PHP sessiont, de csak akkor, ha még nem fut.
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Érték mentése a sessionbe, például user_id vagy role.
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // Érték lekérése a sessionből.
    // Ha nincs ilyen kulcs, a megadott alapértelmezett értéket adja vissza.
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    // Egy session érték törlése.
    public static function delete($key)
    {
        unset($_SESSION[$key]);
    }

    // Teljes kijelentkezés.
    // Törli a session változókat, a session sütit és végül magát a sessiont is.
    public static function destroy()
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    // Igazat ad vissza, ha van user_id a sessionben.
    public static function isLoggedIn()
    {
        return self::get('user_id') !== null;
    }

    // Igazat ad vissza, ha a bejelentkezett felhasználó admin szerepkörű.
    public static function isAdmin()
    {
        return self::get('role') === 'admin';
    }

    // Védett oldalakon használjuk.
    // Ha nincs bejelentkezés, átirányít a login oldalra.
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header('Location: /game-review-php-main/admin/login.php');
            exit;
        }
    }

    // Admin oldalak védelme.
    // Először bejelentkezést kér, utána ellenőrzi az admin jogosultságot.
    public static function requireAdmin()
    {
        self::requireLogin();

        if (!self::isAdmin()) {
            header('Location: /game-review-php-main/admin/index.php');
            exit;
        }
    }
}
