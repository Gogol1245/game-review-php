# Game Reviews dokumentáció

## Projekt felépítése

Ez egy egyszerű PHP + MySQL alapú játékértékelő oldal. A cél az, hogy a látogatók játékokat nézhessenek meg, a bejelentkezett felhasználók recenziót írhassanak, az admin pedig játékokat kezelhessen.

- `index.php`: főoldal, legfrissebb recenziók és játéklista.
- `games.php`: külön játéklista oldal, ahol az összes aktív játék megjelenik.
- `game.php`: egy konkrét játék részletes oldala és a recenzió űrlap.
- `setup.php`: adatbázis és alapadatok létrehozása vagy frissítése.
- `install.sql`: kézzel futtatható SQL telepítő.
- `config/database.php`: adatbázis-kapcsolati beállítások.
- `classes/Database.php`: közös PDO kapcsolat singleton mintával.
- `classes/Game.php`: játékok lekérése, létrehozása, szerkesztése és logikai törlése.
- `classes/Review.php`: recenziók lekérése, mentése és átlagértékelés frissítése.
- `classes/User.php`: bejelentkezési adatok ellenőrzése.
- `classes/Session.php`: session, bejelentkezés és admin jogosultság kezelése.
- `includes/functions.php`: közös segédfüggvények, például HTML escape, dátumformázás és űrlapvalidáció.
- `includes/header.php` és `includes/footer.php`: közös oldalfejléc, CSS és lábléc.
- `admin/`: bejelentkezés, kijelentkezés és admin kezdőlap.
- `admin/games/`: játékok listázása, hozzáadása, szerkesztése és törlése.

## Adatbázis szerkezete

Három fő tábla van:

- `users`: felhasználók. Fontos mezők: `username`, `email`, `password`, `role`.
- `games`: játékok. Fontos mezők: `title`, `slug`, `description`, `developer`, `publisher`, `release_date`, `genre`, `platform`, `image_url`, `rating`, `is_active`.
- `reviews`: recenziók. Fontos mezők: `game_id`, `user_id`, `title`, `content`, `score`, `pros`, `cons`, `is_published`.

A `reviews.game_id` a `games.id` mezőre mutat, a `reviews.user_id` pedig a `users.id` mezőre. Így tudja a rendszer, hogy melyik recenzió melyik játékhoz és melyik felhasználóhoz tartozik.

## Fontosabb osztályok és függvények

- `Database::getInstance()`: singleton metódus. Egy közös adatbázis-kapcsolatot ad vissza, hogy ne kelljen mindenhol új kapcsolatot nyitni.
- `Database::getConnection()`: visszaadja a PDO objektumot, amellyel SQL lekérdezések futnak.
- `Game::getAll()`: aktív játékok listázása.
- `Game::getBySlug()`: egy játék lekérése URL alapján.
- `Game::create()` és `Game::update()`: játék mentése adatbázisba.
- `Game::delete()`: logikai törlés, vagyis `is_active = 0`.
- `Review::create()`: új recenzió mentése és a játék átlagértékelésének frissítése.
- `User::authenticate()`: felhasználónév és jelszó ellenőrzése.
- `Session::requireLogin()`: csak bejelentkezett felhasználónak engedi az oldalt.
- `Session::requireAdmin()`: csak adminnak engedi az oldalt.
- `e()`: HTML kimenet biztonságos kiírása.
- `collectGameFormData()` és `validateGameData()`: admin játékűrlapok egységes kezelése.

## Bejelentkezés és regisztráció működése

A projektben külön nyilvános regisztráció nincs. A `setup.php` létrehoz két alap felhasználót:

- admin: `admin / admin123`
- teszt felhasználó: `user / user123`

Belépéskor az `admin/login.php` elküldi a felhasználónevet és jelszót. A `User::authenticate()` lekéri a felhasználót az adatbázisból, majd a `password_verify()` ellenőrzi a jelszót. Sikeres belépés után a rendszer sessionbe menti a `user_id`, `username` és `role` értékeket.

## Értékelési rendszer

Bejelentkezett felhasználó a `game.php` oldalon írhat recenziót. A pontszám 1 és 10 között lehet. A `Review::create()` menti a recenziót a `reviews` táblába, majd újraszámolja az adott játék átlagpontszámát, és elmenti a `games.rating` mezőbe.

## Adatbázis-kapcsolat működése

A `Database` osztály singleton mintát használ. Ez azt jelenti, hogy a projekt egy közös adatbázis-kapcsolatot használ, amelyet a `Database::getInstance()->getConnection()` hívással lehet elkérni. A kapcsolat PDO-val működik, prepared statementekkel. Ez biztonságosabb, mert a felhasználói adatok nem kerülnek közvetlenül az SQL szövegébe.
