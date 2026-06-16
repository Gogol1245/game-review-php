<?php

// Minden oldal kozvetlenul tolti be a szukseges osztalyokat.
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Game.php';
require_once __DIR__ . '/classes/Review.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

try {
    // A fooldal rovid elonezetet mutat: legujabb recenziok es legujabb jatekok.
    $gameModel = new Game();
    $reviewModel = new Review();
    $games = $gameModel->getAll(6);
    $latestReviews = $reviewModel->getLatest();
} catch (Exception $e) {
    echo '<div style="color:red; padding:20px; background:#ffe6e6; border:1px solid red; margin:20px 0;">';
    echo '<h2>Database Error</h2>';
    echo '<p>' . e($e->getMessage()) . '</p>';
    echo '</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<h1>🎮 Recenzie hier a herné novinky</h1>

<section>
    <h2>⭐ Najnovšie recenzie</h2>
    <?php if (empty($latestReviews)): ?>
        <div class="content-box">
            <p>Zatiaľ nie sú k dispozícii žiadne recenzie. <a href="/game-review-php-main/admin/index.php">Pridajte prvú hru</a>.</p>
        </div>
    <?php else: ?>
        <div class="games-grid">
            <?php foreach ($latestReviews as $review): ?>
                <div class="game-card">
                    <div class="game-card-content">
                        <h3>
                            <a href="/game-review-php-main/game.php?slug=<?= e($review['game_slug']) ?>">
                                <?= e($review['game_title']) ?>
                            </a>
                        </h3>
                        <p><strong><?= e($review['title']) ?></strong></p>
                        <p class="rating">Hodnotenie: <?= e($review['score']) ?>/10</p>
                        <p><small>Autor: <?= e($review['username']) ?></small></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section>
    <h2>🎯 Najnovšie hry</h2>
    <?php if (empty($games)): ?>
        <div class="content-box">
            <p>Zatiaľ nie sú k dispozícii žiadne hry. <a href="/game-review-php-main/admin/games/create.php">Pridajte prvú hru</a>.</p>
        </div>
    <?php else: ?>
        <div class="games-grid">
            <?php foreach ($games as $game): ?>
                <?php renderGameCard($game); ?>
            <?php endforeach; ?>
        </div>

        <p>
            <a href="/game-review-php-main/games.php" class="btn">Všetky hry</a>
        </p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
