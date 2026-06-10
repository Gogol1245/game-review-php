<?php

// Betöltjük az osztályokat és a session kezelőt.
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/Game.php';

// Törlést csak admin végezhet.
Session::start();
Session::requireAdmin();

$gameModel = new Game();
$gameId = (int)($_POST['id'] ?? 0);

// A törlés csak POST kérésre fusson le.
// Ez biztonságosabb, mert egy egyszerű link megnyitása nem módosít adatot.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $gameId > 0) {
    $gameModel->delete($gameId);
}

header('Location: index.php');
exit;
