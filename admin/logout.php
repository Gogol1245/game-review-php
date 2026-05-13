<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/Session.php';

Session::start();
Session::destroy();

header('Location: /game-review-site/admin/login.php');
exit;