<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

try {
    $game = new Game();
    $review = new Review();
    $games = $game->getAll();
    $latestReviews = $review->getLatest();
} catch (Exception $e) {
    echo '<div style="color:red; padding:20px; background:#ffe6e6; border:1px solid red; margin:20px 0;">';
    echo '<h2>Database Error</h2>';
    echo '<p>' . $e->getMessage() . '</p>';
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
            <p>Zatiaľ nie sú k dispozícii žiadne recenzie. <a href="/game-review-site/admin/index.php">Pridajte prvú hru</a>.</p>
        </div>
    <?php else: ?>
        <div class="games-grid">
            <?php foreach ($latestReviews as $rev): ?>
                <div class="game-card">
                    <div class="game-card-content">
                        <h3>
                            <a href="/game-review-site/game.php?slug=<?= e($rev['game_slug']) ?>">
                                <?= e($rev['game_title']) ?>
                            </a>
                        </h3>
                        <p><strong><?= e($rev['title']) ?></strong></p>
                        <p class="rating">Hodnotenie: <?= $rev['score'] ?>/10</p>
                        <p><small>Autor: <?= e($rev['username']) ?></small></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section>
    <h2>🎯 Všetky hry</h2>
    <?php if (empty($games)): ?>
        <div class="content-box">
            <p>Zatiaľ nie sú k dispozícii žiadne hry. <a href="/game-review-site/admin/games/create.php">Pridajte prvú hru</a>.</p>
        </div>
    <?php else: ?>
        <div class="games-grid">
            <?php foreach ($games as $g): ?>
                <div class="game-card">
                    <?php if ($g['image_url']): ?>
                        <img src="<?= e($g['image_url']) ?>" alt="<?= e($g['title']) ?>">
                    <?php else: ?>
                        <div style="height:200px; background:#1a1a2e; display:flex; align-items:center; justify-content:center; color:white; font-size:48px;">🎮</div>
                    <?php endif; ?>
                    <div class="game-card-content">
                        <h3>
                            <a href="/game-review-site/game.php?slug=<?= e($g['slug']) ?>">
                                <?= e($g['title']) ?>
                            </a>
                        </h3>
                        <p><strong>Žáner:</strong> <?= e($g['genre']) ?></p>
                        <p><strong>Platforma:</strong> <?= e($g['platform']) ?></p>
                        <?php if ($g['rating'] > 0): ?>
                            <p class="rating">Hodnotenie: <?= $g['rating'] ?>/10</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>