<?php

// Jatek reszletezo oldal. A bejelentkezett felhasznalok recenzio bekuldeset is ez kezeli.
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Game.php';
require_once __DIR__ . '/classes/Review.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/Session.php';

Session::start();

$gameModel = new Game();
$reviewModel = new Review();

$slug = $_GET['slug'] ?? '';
$message = '';
$messageType = '';

if ($slug === '') {
    header('Location: /game-review-php-main/index.php');
    exit;
}

$game = $gameModel->getBySlug($slug);

if (!$game) {
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="content-box">';
    echo '<h1>Hra nebola nájdená</h1>';
    echo '<p><a href="/game-review-php-main/index.php">Späť na hlavnú stránku</a></p>';
    echo '</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Recenziot csak bejelentkezett felhasznalo kuldhet be.
// A szerzo a sessionbol jon, nem egy szabadon kitoltheto nev mezobol.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!Session::isLoggedIn()) {
        header('Location: /game-review-php-main/admin/index.php');
        exit;
    }

    $title = cleanText($_POST['review_title'] ?? '');
    $content = cleanText($_POST['review_content'] ?? '');
    $score = (int)($_POST['score'] ?? 0);
    $pros = cleanText($_POST['pros'] ?? '');
    $cons = cleanText($_POST['cons'] ?? '');

    if ($title === '' || $content === '' || $score < 1 || $score > 10) {
        $message = 'Vyplňte všetky povinné polia a zadajte hodnotenie 1-10.';
        $messageType = 'error';
    } else {
        try {
            $reviewModel->create($game['id'], Session::get('user_id'), $title, $content, $score, $pros, $cons);

            $message = 'Vaša recenzia bola úspešne pridaná.';
            $messageType = 'success';
            $game = $gameModel->getBySlug($slug);
        } catch (Exception $e) {
            $message = 'Chyba pri ukladaní recenzie: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

$reviews = $reviewModel->getByGameId($game['id']);
require_once __DIR__ . '/includes/header.php';
?>

<div class="content-box">
    <h1><?= e($game['title']) ?></h1>

    <?php if (!empty($game['image_url'])): ?>
        <img src="<?= e($game['image_url']) ?>" alt="<?= e($game['title']) ?>" style="width:100%; max-height:420px; object-fit:cover; margin:20px 0; border-radius:8px;">
    <?php endif; ?>

    <div style="background:#f9f9f9; padding:20px; margin:20px 0; border-radius:8px;">
        <p><strong>Vývojár:</strong> <?= e($game['developer']) ?></p>
        <p><strong>Vydavateľ:</strong> <?= e($game['publisher']) ?></p>
        <p><strong>Dátum vydania:</strong> <?= formatDate($game['release_date']) ?></p>
        <p><strong>Žáner:</strong> <?= e($game['genre']) ?></p>
        <p><strong>Platforma:</strong> <?= e($game['platform']) ?></p>
        <?php if ($game['rating'] > 0): ?>
            <p><strong>Priemerné hodnotenie:</strong> <?= e($game['rating']) ?>/10</p>
        <?php endif; ?>
    </div>

    <h2>Popis hry</h2>
    <p><?= nl2br(e($game['description'])) ?></p>
</div>

<div class="content-box" style="margin-top:20px;">
    <h2>Užívateľské recenzie (<?= count($reviews) ?>)</h2>

    <?php if (empty($reviews)): ?>
        <p>Zatiaľ neboli pridané žiadne recenzie. Buďte prvý!</p>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div style="border-bottom:2px solid #eee; padding:20px 0; margin:20px 0;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:20px;">
                    <h3 style="margin:0;"><?= e($review['title']) ?></h3>
                    <span style="font-size:24px; color:#ffd700; white-space:nowrap;"><?= renderStars($review['score']) ?></span>
                </div>
                <p style="font-size:18px; font-weight:bold; color:#e94560; margin:10px 0;">Hodnotenie: <?= e($review['score']) ?>/10</p>
                <p><?= nl2br(e($review['content'])) ?></p>
                <?php if (!empty($review['pros'])): ?>
                    <p style="color:green;"><strong>Plusy:</strong> <?= e($review['pros']) ?></p>
                <?php endif; ?>
                <?php if (!empty($review['cons'])): ?>
                    <p style="color:red;"><strong>Mínusy:</strong> <?= e($review['cons']) ?></p>
                <?php endif; ?>
                <p><small>Autor: <?= e($review['username']) ?> | <?= formatDate($review['created_at']) ?></small></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="content-box" style="margin-top:20px;">
    <h2>Pridať recenziu</h2>

    <?php if ($message): ?>
        <div style="padding:15px; margin:15px 0; border-radius:8px; <?= $messageType === 'success' ? 'background:#d4edda; color:#155724; border:1px solid #c3e6cb;' : 'background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;' ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <?php if (!Session::isLoggedIn()): ?>
        <p>Na pridanie recenzie sa musíte prihlásiť.</p>
        <a href="/game-review-php-main/admin/index.php" class="btn">Prihlásiť sa</a>
    <?php else: ?>
        <p>Recenziu pridávate ako <strong><?= e(Session::get('username')) ?></strong>.</p>
        <form method="POST" class="admin-form">
            <label>Nadpis recenzie</label>
            <input type="text" name="review_title" placeholder="Napíšte nadpis recenzie" required>

            <label>Text recenzie</label>
            <textarea name="review_content" rows="5" placeholder="Napíšte vašu recenziu..." required></textarea>

            <label>Hodnotenie (1-10)</label>
            <div style="display:flex; gap:10px; margin:10px 0; flex-wrap:wrap;">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <label style="display:flex; flex-direction:column; align-items:center; cursor:pointer;">
                        <input type="radio" name="score" value="<?= $i ?>" required style="width:auto;">
                        <span style="font-size:20px;"><?= $i ?></span>
                    </label>
                <?php endfor; ?>
            </div>

            <label>Plusy (oddeľte čiarkou)</label>
            <input type="text" name="pros" placeholder="Napíšte plusy hry">

            <label>Mínusy (oddeľte čiarkou)</label>
            <input type="text" name="cons" placeholder="Napíšte mínusy hry">

            <button type="submit" name="submit_review" class="btn" style="font-size:18px; padding:12px 24px;">Odoslať recenziu</button>
        </form>
    <?php endif; ?>
</div>

<p style="margin-top:20px;"><a href="/game-review-php-main/index.php" class="btn">Späť na hlavnú stránku</a></p>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
