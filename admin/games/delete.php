<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Game.php';
require_once __DIR__ . '/../../classes/Session.php';

Session::start();
Session::requireLogin();

$game = new Game();
$id = $_GET['id'] ?? 0;

if ($id) {
    $game->delete((int)$id);
}

header('Location: /admin/games/');
exit;