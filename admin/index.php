<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Session.php';

Session::start();
Session::requireLogin();

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Administrácia</h1>
<p>Vitajte, <?= htmlspecialchars(Session::get('username')) ?>!</p>

<div style="margin-top: 20px;">
    <a href="/admin/games/" class="btn">Spravovať hry</a>
    <a href="/admin/logout.php" class="btn">Odhlásiť sa</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>