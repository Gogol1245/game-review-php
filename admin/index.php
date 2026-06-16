<?php

// Egyetlen admin belepesi pont:
// - kijelentkezett allapotban megjeleniti a belepesi urlapot
// - belepes utan admin vagy user panelt mutat
// - a kijelentkezest az ?action=logout kezeli
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Session.php';
require_once __DIR__ . '/../classes/User.php';

Session::start();

if (($_GET['action'] ?? '') === 'logout') {
    User::logout();
    header('Location: /game-review-php-main/admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Vyplňte všetky polia.';
    } else {
        $userModel = new User();

        if ($userModel->login($username, $password)) {
            header('Location: /game-review-php-main/admin/index.php');
            exit;
        }

        $error = 'Nesprávne prihlasovacie údaje.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<?php if (!Session::isLoggedIn()): ?>
    <div class="content-box" style="max-width:460px; margin:40px auto;">
        <h1>Administrácia</h1>

        <?php if ($error): ?>
            <p style="color:#721c24; background:#f8d7da; padding:10px; border-radius:4px; margin-bottom:15px;">
                <?= e($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST" class="admin-form" style="box-shadow:none;">
            <input type="hidden" name="login" value="1">

            <label>Používateľské meno</label>
            <input type="text" name="username" required>

            <label>Heslo</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn">Prihlásiť sa</button>
            <a href="/game-review-php-main/index.php" class="btn">Späť na web</a>
        </form>
    </div>
<?php else: ?>
    <div class="content-box">
        <h1>Administrácia</h1>
        <p>Vitajte, <strong><?= e(Session::get('username')) ?></strong>!</p>

        <?php if (!Session::isAdmin()): ?>
            <p style="background:#f9f9f9; padding:15px; border-radius:8px; margin-top:20px;">
                Ste prihlásený ako používateľ. Môžete písať recenzie k existujúcim hrám, ale nemôžete pridávať, upravovať ani mazať hry.
            </p>
        <?php endif; ?>

        <div style="margin-top:30px;">
            <?php if (Session::isAdmin()): ?>
                <a href="/game-review-php-main/admin/games/index.php" class="btn">Spravovať hry</a>
            <?php endif; ?>
            <a href="/game-review-php-main/index.php" class="btn">Prejsť na web</a>
            <a href="/game-review-php-main/admin/index.php?action=logout" class="btn">Odhlásiť sa</a>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
