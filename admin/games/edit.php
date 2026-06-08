<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/Game.php';

Session::start();
Session::requireAdmin();

$gameClass = new Game();
$message = '';

$id = $_GET['id'] ?? 0;
$game = $gameClass->getById((int)$id);

if (!$game) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'developer' => $_POST['developer'] ?? '',
        'publisher' => $_POST['publisher'] ?? '',
        'release_date' => $_POST['release_date'] ?? '',
        'genre' => $_POST['genre'] ?? '',
        'platform' => $_POST['platform'] ?? '',
        'image_url' => $_POST['image_url'] ?? ''
    ];
    
    if (empty($data['title'])) {
        $message = 'Názov hry je povinný.';
    } else {
        if ($gameClass->update((int)$id, $data)) {
            header('Location: index.php');
            exit;
        } else {
            $message = 'Chyba pri aktualizácii hry.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<h1>Upraviť hru: <?= e($game['title']) ?></h1>

<?php if ($message): ?>
    <p style="color:red; background:#ffe6e6; padding:10px;"><?= e($message) ?></p>
<?php endif; ?>

<form method="POST" class="admin-form">
    <label>Názov hry *</label>
    <input type="text" name="title" value="<?= e($game['title']) ?>" required>
    
    <label>Popis</label>
    <textarea name="description" rows="5"><?= e($game['description']) ?></textarea>
    
    <label>Vývojár</label>
    <input type="text" name="developer" value="<?= e($game['developer']) ?>">
    
    <label>Vydavateľ</label>
    <input type="text" name="publisher" value="<?= e($game['publisher']) ?>">
    
    <label>Dátum vydania</label>
    <input type="date" name="release_date" value="<?= e($game['release_date']) ?>">
    
    <label>Žáner</label>
    <input type="text" name="genre" value="<?= e($game['genre']) ?>">
    
    <label>Platforma</label>
    <input type="text" name="platform" value="<?= e($game['platform']) ?>">
    
    <label>URL obrázka</label>
    <input type="url" name="image_url" value="<?= e($game['image_url']) ?>">
    
    <button type="submit" class="btn">Uložiť zmeny</button>
    <a href="index.php" class="btn">Späť</a>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
