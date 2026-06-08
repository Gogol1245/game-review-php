<?php

// A kijelentkezéshez csak a session kezelőre van szükség.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/Session.php';

// Elindítjuk, majd teljesen töröljük a sessiont.
Session::start();
Session::destroy();

header('Location: /game-review-php-main/admin/login.php');
exit;
