<?php

// Kozponti segedosztaly a bejelentkezett felhasznalo allapotahoz.
class Session
{
    // Elinditja a PHP sessiont, ha meg nem aktiv.
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Egy erteket ment az aktualis sessionbe.
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // Egy erteket olvas ki az aktualis sessionbol.
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    // Egy erteket torol az aktualis sessionbol.
    public static function delete($key)
    {
        unset($_SESSION[$key]);
    }

    // Minden session adatot torol. A jelenlegi folyamatban a User::logout() hivja.
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

    // A latogato akkor van bejelentkezve, ha van user_id a sessionben.
    public static function isLoggedIn()
    {
        return self::get('user_id') !== null;
    }

    // Az admin oldalak ezt a szerepkor-ellenorzest hasznaljak.
    public static function isAdmin()
    {
        return self::get('role') === 'admin';
    }

    // A vedett oldalak az admin/index.php-ra iranyitanak, ott van a belepesi urlap.
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header('Location: /game-review-php-main/admin/index.php');
            exit;
        }
    }

    // A jatekkezelo oldalakhoz bejelentkezes es admin szerepkor is kell.
    public static function requireAdmin()
    {
        self::requireLogin();

        if (!self::isAdmin()) {
            header('Location: /game-review-php-main/admin/index.php');
            exit;
        }
    }
}
