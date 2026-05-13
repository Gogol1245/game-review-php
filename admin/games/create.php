<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/Game.php';

Session::start();

if (!Session::isLoggedIn()) {
    header('Location: /game-review-site/admin/login.php');
    exit;
}

$game = new Game();
$message = '';

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
        $id = $game->create($data);
        if ($id) {
            header('Location: /game-review-site/admin/games/index.php');
            exit;
        } else {
            $message = 'Chyba pri vytváraní hry.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="content-box">
    <h1>➕ Pridať novú hru</h1>
    
    <?php if ($message): ?>
        <p style="color:red; background:#ffe6e6; padding:10px; border-radius:4px;"><?= e($message) ?></p>
    <?php endif; ?>
    
    <form method="POST" class="admin-form">
        <label>🎮 Názov hry *</label>
        <input type="text" name="title" required>
        
        <label>📝 Popis</label>
        <textarea name="description" rows="5"></textarea>
        
        <label>👨‍💻 Vývojár</label>
        <input type="text" name="developer">
        
        <label>📦 Vydavateľ</label>
        <input type="text" name="publisher">
        
        <label>📅 Dátum vydania</label>
        <input type="date" name="release_date">
        
        <label>🎯 Žáner</label>
        <input type="text" name="genre">
        
        <label>💻 Platforma</label>
        <input type="text" name="platform">
        
        <label>🖼️ URL obrázka</label>
        <input type="url" name="image_url" placeholder="https://example.com/image.jpg">
        
        <button type="submit" class="btn">✅ Vytvoriť hru</button>
        <a href="/game-review-site/admin/games/index.php" class="btn">← Späť</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>