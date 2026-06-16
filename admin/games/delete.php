<?php

// Csak admin altal hasznalhato logikai torlesi vegpont. A lista oldal POST-tal kuld ide.
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Game.php';
require_once __DIR__ . '/../../classes/Session.php';

Session::start();
Session::requireAdmin();

$gameModel = new Game();
$gameId = (int)($_POST['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $gameId > 0) {
    $gameModel->delete($gameId);
}

header('Location: index.php');
exit;
