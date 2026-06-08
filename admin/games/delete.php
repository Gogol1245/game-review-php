<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/Game.php';

Session::start();
Session::requireAdmin();

$game = new Game();
$id = $_GET['id'] ?? 0;

if ($id) {
    $game->delete((int)$id);
}

header('Location: index.php');
exit;
