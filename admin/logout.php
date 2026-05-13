<?php
require_once __DIR__ . '/../classes/Session.php';

Session::start();
Session::destroy();

header('Location: /admin/login.php');
exit;