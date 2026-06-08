<?php

// Betöltjük az autoloadot, a session kezelőt és a User osztályt.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/Session.php';
require_once __DIR__ . '/../classes/User.php';

// A session kell ahhoz, hogy sikeres belépés után megjegyezzük a felhasználót.
Session::start();

// Ha a felhasználó már be van jelentkezve, nincs szükség új belépésre.
if (Session::isLoggedIn()) {
    header('Location: /game-review-php-main/admin/index.php');
    exit;
}

$error = '';

// A POST kérés azt jelenti, hogy a belépési űrlapot elküldték.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Vyplňte všetky polia.';
    } else {
        // A User osztály ellenőrzi a felhasználónevet és a jelszót.
        $userModel = new User();
        $authenticatedUser = $userModel->authenticate($username, $password);

        if ($authenticatedUser) {
            // Sikeres belépésnél a legfontosabb adatokat sessionbe mentjük.
            Session::set('user_id', $authenticatedUser['id']);
            Session::set('username', $authenticatedUser['username']);
            Session::set('role', $authenticatedUser['role']);

            header('Location: /game-review-php-main/admin/index.php');
            exit;
        }

        $error = 'Nesprávne prihlasovacie údaje.';
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prihlásenie - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #1a1a2e; }
        .login-form { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 20px rgba(0,0,0,0.3); width: 100%; max-width: 400px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #ddd; border-radius: 4px; font-size: 16px; }
        button { width: 100%; padding: 12px; background: #e94560; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #c73e54; }
        .error { color: #721c24; margin-bottom: 15px; background: #f8d7da; padding: 10px; border-radius: 4px; }
        h2 { text-align: center; margin-bottom: 20px; color: #1a1a2e; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>🔐 Administrácia</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Používateľské meno" required>
            <input type="password" name="password" placeholder="Heslo" required>
            <button type="submit">Prihlásiť sa</button>
        </form>
        <p style="margin-top:15px; text-align:center;"><a href="/game-review-php-main/index.php">← Späť na hlavnú stránku</a></p>
    </div>
</body>
</html>
