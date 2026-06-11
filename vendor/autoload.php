<?php
// Az autoload.php automatikusan betölti a classes mappában lévő osztályokat.
// Ez azért hasznos, mert így nem kell minden oldalon külön require_once sorral betölteni
// például a Game.php, Review.php, User.php, Session.php vagy Database.php fájlt.
//
// Példa:
// Ha a kódban ezt írjuk:
//     $gameModel = new Game();
// akkor ez az autoload függvény megkeresi a classes/Game.php fájlt,
// és automatikusan betölti require_once segítségével.
//
// Röviden:
// autoload.php = automatikus osztálybetöltő.
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
