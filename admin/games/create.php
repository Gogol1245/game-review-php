<?php

// Betöltjük az osztályokat, segédfüggvényeket és a session kezelőt.
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/Game.php';

// Új játékot csak admin hozhat létre.
Session::start();
Session::requireAdmin();

$gameModel = new Game();
$errors = [];
$formData = [
    'title' => '',
    'description' => '',
    'developer' => '',
    'publisher' => '',
    'release_date' => '',
    'genre' => '',
    'platform' => '',
    'image_url' => '',
    'rating' => 0,
];

// POST esetén az admin elküldte az új játék űrlapját.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = collectGameFormData($_POST);
    $formData['rating'] = (float)($_POST['rating'] ?? 0);
    $errors = validateGameData($formData);

    if (empty($errors)) {
        $newGameId = $gameModel->create($formData);

        if ($newGameId) {
            header('Location: /game-review-php-main/admin/games/index.php');
            exit;
        }

        $errors[] = 'Chyba pri vytváraní hry.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="content-box">
    <h1>➕ Pridať novú hru</h1>

    <?php if ($errors): ?>
        <div style="color:#721c24; background:#f8d7da; padding:10px; border-radius:4px;">
            <?php foreach ($errors as $error): ?>
                <p><?= e($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="admin-form">
        <label>🎮 Názov hry *</label>
        <input type="text" name="title" value="<?= e($formData['title']) ?>" required>

        <label>📝 Popis</label>
        <textarea name="description" rows="5"><?= e($formData['description']) ?></textarea>

        <label>👨‍💻 Vývojár</label>
        <input type="text" name="developer" value="<?= e($formData['developer']) ?>">

        <label>📦 Vydavateľ</label>
        <input type="text" name="publisher" value="<?= e($formData['publisher']) ?>">

        <label>📅 Dátum vydania</label>
        <input type="date" name="release_date" value="<?= e($formData['release_date']) ?>">

        <label>🎯 Žáner</label>
        <input type="text" name="genre" value="<?= e($formData['genre']) ?>">

        <label>💻 Platforma</label>
        <input type="text" name="platform" value="<?= e($formData['platform']) ?>">

        <label>⭐ Hodnotenie (0-10)</label>
        <input type="number" name="rating" value="<?= e($formData['rating']) ?>" min="0" max="10" step="0.1">

        <label>🖼️ URL obrázka</label>
        <input type="url" name="image_url" value="<?= e($formData['image_url']) ?>" placeholder="https://example.com/image.jpg">

        <button type="submit" class="btn">✅ Vytvoriť hru</button>
        <a href="/game-review-php-main/admin/games/index.php" class="btn">← Späť</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
