<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Game.php';
require_once __DIR__ . '/../../classes/Session.php';

Session::start();
Session::requireLogin();

$game = new Game();
$games = $game->getAll(100);

require_once __DIR__ . '/../../includes/header.php';
?>

<h1>Správa hier</h1>
<a href="/admin/games/create.php" class="btn">Pridať novú hru</a>
<a href="/admin/" class="btn">Späť na admin</a>

<table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
    <thead>
        <tr style="background: #f5f5f5;">
            <th style="padding: 10px; text-align: left;">ID</th>
            <th style="padding: 10px; text-align: left;">Názov</th>
            <th style="padding: 10px; text-align: left;">Žáner</th>
            <th style="padding: 10px; text-align: left;">Akcie</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($games as $game): ?>
        <tr>
            <td style="padding: 10px;"><?= $game['id'] ?></td>
            <td style="padding: 10px;"><?= htmlspecialchars($game['title']) ?></td>
            <td style="padding: 10px;"><?= htmlspecialchars($game['genre']) ?></td>
            <td style="padding: 10px;">
                <a href="/admin/games/edit.php?id=<?= $game['id'] ?>">Upraviť</a>
                <a href="/admin/games/delete.php?id=<?= $game['id'] ?>" onclick="return confirm('Naozaj chcete vymazať túto hru?')">Vymazať</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>