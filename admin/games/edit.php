<?php

// Betöltjük az osztályokat, segédfüggvényeket és a session kezelőt.
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/Game.php';

// Játékot csak admin szerkeszthet.
Session::start();
Session::requireAdmin();

$gameModel = new Game();
$errors = [];

// Az ID GET paraméterből érkezik, például edit.php?id=5.
$gameId = (int)($_GET['id'] ?? 0);
$game = $gameModel->getById($gameId);

if (!$game) {
    header('Location: index.php');
    exit;
}

$formData = $game;

// POST esetén az admin elküldte a szerkesztett adatokat.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = collectGameFormData($_POST);
    $formData['rating'] = (float)($_POST['rating'] ?? 0);
    $errors = validateGameData($formData);

    if (empty($errors)) {
        if ($gameModel->update($gameId, $formData)) {
            header('Location: index.php');
            exit;
        }

        $errors[] = 'Chyba pri aktualizácii hry.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<h1>Upraviť hru: <?= e($game['title']) ?></h1>

<?php if ($errors): ?>
    <div style="color:#721c24; background:#f8d7da; padding:10px; border-radius:4px;">
        <?php foreach ($errors as $error): ?>
            <p><?= e($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" class="admin-form">
    <label>Názov hry *</label>
    <input type="text" name="title" value="<?= e($formData['title']) ?>" required>

    <label>Popis</label>
    <textarea name="description" rows="5"><?= e($formData['description']) ?></textarea>

    <label>Vývojár</label>
    <input type="text" name="developer" value="<?= e($formData['developer']) ?>">

    <label>Vydavateľ</label>
    <input type="text" name="publisher" value="<?= e($formData['publisher']) ?>">

    <label>Dátum vydania</label>
    <input type="date" name="release_date" value="<?= e($formData['release_date']) ?>">

    <label>Žáner</label>
    <input type="text" name="genre" value="<?= e($formData['genre']) ?>">

    <label>Platforma</label>
    <input type="text" name="platform" value="<?= e($formData['platform']) ?>">

    <label>Hodnotenie (0-10)</label>
    <input type="number" name="rating" value="<?= e($formData['rating']) ?>" min="0" max="10" step="0.1">

    <label>URL obrázka</label>
    <input type="url" name="image_url" value="<?= e($formData['image_url']) ?>">

    <button type="submit" class="btn">Uložiť zmeny</button>
    <a href="index.php" class="btn">Späť</a>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
