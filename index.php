<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Manual autoload if vendor doesn't exist
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // Manual class loading
    spl_autoload_register(function ($class) {
        $file = __DIR__ . '/classes/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

try {
    $game = new Game();
    $review = new Review();
    
    $games = $game->getAll();
    $latestReviews = $review->getLatest();
} catch (Exception $e) {
    echo "<div style='color:red; padding:20px; background:#ffe6e6; border:1px solid red; margin:20px 0;'>";
    echo "<h2>Database Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Please run <a href='setup.php'>setup.php</a> to create the database.</p>";
    echo "</div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<h1>Recenzie hier a herné novinky</h1>

<section>
    <h2>Najnovšie recenzie</h2>
    <?php if (empty($latestReviews)): ?>
        <p>Zatiaľ nie sú k dispozícii žiadne recenzie. Pridajte ich cez administráciu.</p>
    <?php else: ?>
        <div class="games-grid">
            <?php foreach ($latestReviews as $rev): ?>
                <div class="game-card">
                    <div class="game-card-content">
                        <h3>
                            <a href="game.php?slug=<?= e($rev['game_slug']) ?>">
                                <?= e($rev['game_title']) ?>
                            </a>
                        </h3>
                        <p><?= e($rev['title']) ?></p>
                        <p class="rating">Hodnotenie: <?= $rev['score'] ?>/10</p>
                        <p><small>Autor: <?= e($rev['username']) ?></small></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section>
    <h2>Všetky hry</h2>
    <?php if (empty($games)): ?>
        <p>Zatiaľ nie sú k dispozícii žiadne hry. <a href="admin/">Pridajte prvú hru</a>.</p>
    <?php else: ?>
        <div class="games-grid">
            <?php foreach ($games as $g): ?>
                <div class="game-card">
                    <?php if ($g['image_url']): ?>
                        <img src="<?= e($g['image_url']) ?>" alt="<?= e($g['title']) ?>">
                    <?php endif; ?>
                    <div class="game-card-content">
                        <h3>
                            <a href="game.php?slug=<?= e($g['slug']) ?>">
                                <?= e($g['title']) ?>
                            </a>
                        </h3>
                        <p>Žáner: <?= e($g['genre']) ?></p>
                        <p>Platforma: <?= e($g['platform']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>