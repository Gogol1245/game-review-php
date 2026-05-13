<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/Session.php';

Session::start();

// If not logged in, redirect to login
if (!Session::isLoggedIn()) {
    header('Location: /game-review-site/admin/login.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="content-box">
    <h1>🔧 Administrácia</h1>
    <p>Vitajte, <strong><?= e(Session::get('username')) ?></strong>!</p>
    
    <div style="margin-top: 30px;">
        <a href="/game-review-site/admin/games/index.php" class="btn">🎮 Spravovať hry</a>
        <a href="/game-review-site/admin/logout.php" class="btn">🚪 Odhlásiť sa</a>
        <a href="/game-review-site/index.php" class="btn">🏠 Prejsť na web</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>