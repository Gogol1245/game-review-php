<?php

// Csak adminoknak elerheto jateklista szerkesztesi es logikai torlesi muveletekkel.
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Game.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Session.php';

Session::start();
Session::requireAdmin();

$gameModel = new Game();
$games = $gameModel->getAll(100);

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
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?= e($game['id']) ?></td>
                        <td><a href="/game-review-php-main/game.php?slug=<?= e($game['slug']) ?>"><?= e($game['title']) ?></a></td>
                        <td><?= e($game['genre']) ?></td>
                        <td><?= e($game['platform']) ?></td>
                        <td>
                            <a href="/game-review-php-main/admin/games/edit.php?id=<?= e($game['id']) ?>" class="btn">✏️ Upraviť</a>
                            <form method="POST" action="/game-review-php-main/admin/games/delete.php" style="display:inline;" onsubmit="return confirm('Naozaj chcete vymazať?')">
                                <input type="hidden" name="id" value="<?= e($game['id']) ?>">
                                <button type="submit" class="btn" style="background:#ff4444;">🗑️ Vymazať</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
