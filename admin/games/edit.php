<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Game.php';
require_once __DIR__ . '/../../classes/Session.php';

Session::start();
Session::requireLogin();

$gameClass = new Game();
$message = '';

$id = $_GET['id'] ?? 0;
$game = $gameClass->getById((int)$id);

if (!$game) {
    header('Location: /admin/games/');
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
            header('Location: /admin/games/');
            exit;
        } else {
            $message = 'Chyba pri aktualizácii hry.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<h1>Upraviť hru: <?= htmlspecialchars($game['title']) ?></h1>

<?php if ($message): ?>
    <p style="color: red;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST" class="admin-form">
    <label>Názov hry *</label>
    <input type="text" name="title" value="<?= htmlspecialchars($game['title']) ?>" required>
    
    <label>Popis</label>
    <textarea name="description" rows="5"><?= htmlspecialchars($game['description']) ?></textarea>
    
    <label>Vývojár</label>
    <input type="text" name="developer" value="<?= htmlspecialchars($game['developer']) ?>">
    
    <label>Vydavateľ</label>
    <input type="text" name="publisher" value="<?= htmlspecialchars($game['publisher']) ?>">
    
    <label>Dátum vydania</label>
    <input type="date" name="release_date" value="<?= htmlspecialchars($game['release_date']) ?>">
    
    <label>Žáner</label>
    <input type="text" name="genre" value="<?= htmlspecialchars($game['genre']) ?>">
    
    <label>Platforma</label>
    <input type="text" name="platform" value="<?= htmlspecialchars($game['platform']) ?>">
    
    <label>URL obrázka</label>
    <input type="url" name="image_url" value="<?= htmlspecialchars($game['image_url']) ?>">
    
    <button type="submit" class="btn">Uložiť zmeny</button>
    <a href="/admin/games/" class="btn">Späť</a>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>