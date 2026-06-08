<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/Game.php';

Session::start();

Session::requireAdmin();

$game = new Game();
$games = $game->getAll(100);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="content-box">
    <h1>🎮 Správa hier</h1>
    <a href="/game-review-php-main/admin/games/create.php" class="btn">➕ Pridať novú hru</a>
    <a href="/game-review-php-main/admin/index.php" class="btn">← Späť na admin</a>
    <a href="/game-review-php-main/index.php" class="btn">🏠 Domov</a>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Názov</th>
                <th>Žáner</th>
                <th>Platforma</th>
                <th>Akcie</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($games)): ?>
                <tr><td colspan="5" style="text-align:center;">Zatiaľ nie sú pridané žiadne hry.</td></tr>
            <?php else: ?>
                <?php foreach ($games as $g): ?>
                <tr>
                    <td><?= $g['id'] ?></td>
                    <td><a href="/game-review-php-main/game.php?slug=<?= e($g['slug']) ?>"><?= e($g['title']) ?></a></td>
                    <td><?= e($g['genre']) ?></td>
                    <td><?= e($g['platform']) ?></td>
                    <td>
                        <a href="/game-review-php-main/admin/games/edit.php?id=<?= $g['id'] ?>" class="btn">✏️ Upraviť</a>
                        <a href="/game-review-php-main/admin/games/delete.php?id=<?= $g['id'] ?>" class="btn" onclick="return confirm('Naozaj chcete vymazať?')" style="background:#ff4444;">🗑️ Vymazať</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
