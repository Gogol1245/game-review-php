<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Game.php';
require_once __DIR__ . '/classes/Review.php';
require_once __DIR__ . '/includes/functions.php'; // Added this line
require_once __DIR__ . '/includes/header.php';

$gameClass = new Game();
$reviewClass = new Review();

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    redirect('/'); // Using function from functions.php
}

$game = $gameClass->getBySlug($slug);

if (!$game) {
    header('HTTP/1.0 404 Not Found');
    echo '<h1>Hra nebola nájdená</h1>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$reviews = $reviewClass->getByGameId($game['id']);
?>

<article>
    <h1><?= e($game['title']) ?></h1> <!-- Using e() function -->
    
    <?php if ($game['image_url'] && isValidUrl($game['image_url'])): ?>
        <img src="<?= e($game['image_url']) ?>" alt="<?= e($game['title']) ?>" style="max-width: 100%; height: auto;">
    <?php endif; ?>
    
    <div class="game-details">
        <p><strong>Vývojár:</strong> <?= e($game['developer']) ?></p>
        <p><strong>Vydavateľ:</strong> <?= e($game['publisher']) ?></p>
        <p><strong>Dátum vydania:</strong> <?= formatDate($game['release_date'], 'long') ?></p>
        <p><strong>Žáner:</strong> <?= e($game['genre']) ?></p>
        <p><strong>Platforma:</strong> <?= e($game['platform']) ?></p>
    </div>
    
    <div class="description">
        <h2>Popis hry</h2>
        <?= nl2br(e($game['description'])) ?>
    </div>
    
    <section class="reviews">
        <h2>Recenzie (<?= count($reviews) ?>)</h2>
        
        <?php if (empty($reviews)): ?>
            <p>Zatiaľ neboli pridané žiadne recenzie.</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h3><?= e($review['title']) ?></h3>
                    <p class="rating">Hodnotenie: <?= renderStars($review['score']) ?> (<?= $review['score'] ?>/10)</p>
                    <p><?= nl2br(e($review['content'])) ?></p>
                    <p><small>Autor: <?= e($review['username']) ?> | Dátum: <?= formatDate($review['created_at']) ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>