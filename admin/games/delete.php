<?php

// Betöltjük az osztályokat és a session kezelőt.
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/Game.php';

// Törlést csak admin végezhet.
Session::start();
Session::requireAdmin();

$gameModel = new Game();
$gameId = (int)($_GET['id'] ?? 0);

// A delete() logikai törlést végez: is_active = 0.
if ($gameId > 0) {
    $gameModel->delete($gameId);
}

header('Location: index.php');
exit;
