<?php

// Teljes publikus jateklista. A szukseges osztalyokat kozvetlenul toltjuk be.
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Game.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

try {
    $gameModel = new Game();
    $games = $gameModel->getAll(200);
} catch (Exception $e) {
    echo '<div style="color:red; padding:20px; background:#ffe6e6; border:1px solid red; margin:20px 0;">';
    echo '<h2>Database Error</h2>';
    echo '<p>' . e($e->getMessage()) . '</p>';
    echo '</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<h1>🎮 Všetky hry</h1>

<div class="content-box">
    <p>Počet hier v databáze: <strong><?= count($games) ?></strong></p>
</div>

<?php if (empty($games)): ?>
    <div class="content-box" style="margin-top:20px;">
        <p>Zatiaľ nie sú k dispozícii žiadne hry.</p>
    </div>
<?php else: ?>
    <div class="games-grid">
        <?php foreach ($games as $game): ?>
            <?php renderGameCard($game); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
