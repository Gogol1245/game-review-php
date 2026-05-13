<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Session.php';

Session::start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Vyplňte všetky polia.';
    } else {
        $user = new User();
        $authenticated = $user->authenticate($username, $password);
        
        if ($authenticated) {
            Session::set('user_id', $authenticated['id']);
            Session::set('username', $authenticated['username']);
            Session::set('role', $authenticated['role']);
            
            header('Location: /admin/');
            exit;
        } else {
            $error = 'Nesprávne prihlasovacie údaje.';
        }
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
        body { font-family: Arial; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f5f5f5; }
        .login-form { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #e94560; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #c73e54; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Administrácia - Prihlásenie</h2>
        
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Používateľské meno" required>
            <input type="password" name="password" placeholder="Heslo" required>
            <button type="submit">Prihlásiť sa</button>
        </form>
    </div>
</body>
</html>