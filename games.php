<?php
// Fejlesztés közben minden PHP hibát megjelenítünk.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Betöltjük az osztályokat, a közös függvényeket és az oldal fejlécét.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

try {
    // Ez az oldal a teljes aktív játéklistát mutatja.
    // Azért kérünk nagy limitet, hogy ne csak az első 10 játék jelenjen meg.
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
        <!-- Itt már minden aktív játék megjelenik, nem csak a főoldali rövid válogatás. -->
        <?php foreach ($games as $game): ?>
            <div class="game-card">
                <?php if ($game['image_url']): ?>
                    <img src="<?= e($game['image_url']) ?>" alt="<?= e($game['title']) ?>">
                <?php else: ?>
                    <div style="height:200px; background:#1a1a2e; display:flex; align-items:center; justify-content:center; color:white; font-size:48px;">🎮</div>
                <?php endif; ?>

                <div class="game-card-content">
                    <h3>
                        <a href="/game-review-php-main/game.php?slug=<?= e($game['slug']) ?>">
                            <?= e($game['title']) ?>
                        </a>
                    </h3>
                    <p><strong>Žáner:</strong> <?= e($game['genre']) ?></p>
                    <p><strong>Platforma:</strong> <?= e($game['platform']) ?></p>
                    <?php if ($game['rating'] > 0): ?>
                        <p class="rating">Hodnotenie: <?= e($game['rating']) ?>/10</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
