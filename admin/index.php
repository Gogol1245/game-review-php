<?php
// Fejlesztési hibakiírás.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Betöltjük az osztályokat, segédfüggvényeket és a session kezelőt.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/Session.php';

// Az admin kezdőlaphoz legalább bejelentkezés szükséges.
Session::start();
Session::requireLogin();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="content-box">
    <h1>Administrácia</h1>
    <p>Vitajte, <strong><?= e(Session::get('username')) ?></strong>!</p>

    <?php if (!Session::isAdmin()): ?>
        <p style="background:#f9f9f9; padding:15px; border-radius:8px; margin-top:20px;">
            Ste prihlásený ako používateľ. Môžete písať recenzie k existujúcim hrám, ale nemôžete pridávať, upravovať ani mazať hry.
        </p>
    <?php endif; ?>

    <div style="margin-top:30px;">
        <?php if (Session::isAdmin()): ?>
            <a href="/game-review-php-main/admin/games/index.php" class="btn">Spravovať hry</a>
        <?php endif; ?>
        <a href="/game-review-php-main/index.php" class="btn">Prejsť na web</a>
        <a href="/game-review-php-main/admin/logout.php" class="btn">Odhlásiť sa</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
