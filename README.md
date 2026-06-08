# Game Reviews - részletes tanulási dokumentáció

Ez a dokumentáció úgy készült, hogy szóbeli feleléshez vagy projektbemutatóhoz is használható legyen. Nem csak azt írja le, hogy melyik fájl mire való, hanem azt is, hogy a fontosabb PHP, adatbázis és objektumorientált részek miért vannak ott.

## 1. A projekt röviden

Ez egy egyszerű PHP + MySQL alapú játékértékelő weboldal.

A weboldalon:

- a látogató játékokat tud böngészni,
- egy játék saját oldalán meg tudja nézni a részletes adatokat,
- bejelentkezett felhasználó recenziót tud írni,
- admin felhasználó játékokat tud hozzáadni, szerkeszteni és törölni,
- az értékelések alapján a játékok átlagpontszáma frissül.

A projekt nem használ nagy keretrendszert, például Laravelt. Ez szándékosan jó egy iskolai projektnél, mert könnyebb elmagyarázni, hogy mi történik a háttérben.

## 2. Mappaszerkezet

```text
game-review-php-main/
├── admin/
│   ├── games/
│   │   ├── create.php
│   │   ├── delete.php
│   │   ├── edit.php
│   │   └── index.php
│   ├── index.php
│   ├── login.php
│   └── logout.php
├── classes/
│   ├── Database.php
│   ├── Game.php
│   ├── Review.php
│   ├── Session.php
│   └── User.php
├── config/
│   └── database.php
├── includes/
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── vendor/
│   └── autoload.php
├── .htaccess
├── composer.json
├── game.php
├── games.php
├── index.php
├── install.sql
└── README.md
```

## 3. Fájlok szerepe

### `index.php`

Ez a főoldal. Itt jelennek meg:

- a legfrissebb recenziók,
- néhány legújabb játék,
- link a teljes játéklistára.

Fontos részei:

- betölti az osztályokat a `vendor/autoload.php` segítségével,
- betölti a közös függvényeket az `includes/functions.php` fájlból,
- létrehoz egy `Game` objektumot,
- létrehoz egy `Review` objektumot,
- lekéri az adatokat az adatbázisból,
- HTML kártyákban kiírja őket.

Egyszerű folyamat:

```text
index.php megnyílik
↓
autoload betölti az osztályokat
↓
Game::getAll() lekéri a játékokat
↓
Review::getLatest() lekéri a legfrissebb recenziókat
↓
az oldal HTML-ben megjeleníti az adatokat
```

### `games.php`

Ez a külön játéklista oldal. Azért kellett, mert már sok játék van az adatbázisban, és a főoldalon nem jó mindet egyszerre mutatni.

Itt az összes aktív játék megjelenik.

Fontos:

- `Game::getAll(200)` nagyobb limittel kéri le a játékokat,
- csak az `is_active = 1` játékok jelennek meg,
- minden kártya a saját `game.php?slug=...` oldalra linkel.

### `game.php`

Ez egy konkrét játék részletes oldala.

Példa:

```text
game.php?slug=minecraft
```

A `slug` alapján tudja a rendszer, melyik játékot kell betölteni.

Az oldalon látszik:

- játék címe,
- kép,
- fejlesztő,
- kiadó,
- megjelenési dátum,
- műfaj,
- platform,
- átlagértékelés,
- leírás,
- felhasználói recenziók,
- recenzió írására szolgáló űrlap.

Ha a felhasználó nincs bejelentkezve, csak egy belépési linket lát. Ha be van jelentkezve, megjelenik az űrlap.

### `admin/login.php`

Ez a belépési oldal.

Itt a felhasználó megadja:

- felhasználónevét,
- jelszavát.

A fájl meghívja a `User::authenticate()` metódust, amely ellenőrzi az adatbázisban a felhasználót.

Sikeres belépés után sessionbe kerül:

- `user_id`,
- `username`,
- `role`.

### `admin/logout.php`

Ez kijelentkezteti a felhasználót.

Meghívja:

```php
Session::destroy();
```

Ez törli a session adatokat, majd visszairányít a login oldalra.

### `admin/index.php`

Ez az admin kezdőoldal.

Ha a felhasználó admin, látja a játékkezelés linkjét.

Ha csak sima editor, akkor recenziót írhat, de játékot nem tud hozzáadni vagy törölni.

### `admin/games/index.php`

Ez az admin játéklista.

Itt táblázatban látszanak a játékok, és minden játék mellett van:

- szerkesztés,
- törlés.

### `admin/games/create.php`

Új játék hozzáadása.

Az űrlapból érkező adatokat a `collectGameFormData()` összegyűjti, majd a `validateGameData()` ellenőrzi.

Ha minden rendben van, a `Game::create()` elmenti az új játékot az adatbázisba.

### `admin/games/edit.php`

Meglévő játék szerkesztése.

Az ID a linkből érkezik:

```text
edit.php?id=5
```

Ez alapján a `Game::getById()` lekéri a játékot, majd a módosított adatokat a `Game::update()` menti el.

### `admin/games/delete.php`

Játék törlése.

Fontos: ez nem valódi fizikai törlés, hanem logikai törlés.

Ez történik:

```sql
UPDATE games SET is_active = 0 WHERE id = :id
```

Ez azért jó, mert a játék nem tűnik el végleg az adatbázisból, csak nem jelenik meg a weboldalon.

## 4. Az adatbázis táblái

### `users`

Felhasználók táblája.

Fontos mezők:

- `id`: egyedi azonosító,
- `username`: felhasználónév,
- `email`: email cím,
- `password`: titkosított jelszó hash,
- `role`: jogosultság, például `admin` vagy `editor`,
- `created_at`: létrehozás ideje.

Fontos: a jelszó nem sima szövegként van tárolva, hanem hashként.

### `games`

Játékok táblája.

Fontos mezők:

- `id`: játék azonosítója,
- `title`: játék neve,
- `slug`: URL-ben használt rövid név,
- `description`: leírás,
- `developer`: fejlesztő,
- `publisher`: kiadó,
- `release_date`: megjelenési dátum,
- `genre`: kategória vagy műfaj,
- `platform`: platformok,
- `image_url`: kép linkje,
- `rating`: átlagértékelés,
- `is_active`: aktív-e a játék.

Példa slug:

```text
Minecraft -> minecraft
The Witcher 3: Wild Hunt -> the-witcher-3-wild-hunt
```

A slug azért hasznos, mert szebb URL-t ad, mint egy sima szám.

### `reviews`

Recenziók táblája.

Fontos mezők:

- `id`: recenzió azonosítója,
- `game_id`: melyik játékhoz tartozik,
- `user_id`: ki írta,
- `title`: recenzió címe,
- `content`: recenzió szövege,
- `score`: pontszám 1 és 10 között,
- `pros`: pozitívumok,
- `cons`: negatívumok,
- `is_published`: látható-e,
- `created_at`: létrehozás ideje.

Kapcsolatok:

```text
reviews.game_id -> games.id
reviews.user_id -> users.id
```

Ez azt jelenti, hogy egy recenzió mindig kapcsolódik egy játékhoz és egy felhasználóhoz.

## 5. Fontos osztályok

## `Database`

Fájl:

```text
classes/Database.php
```

Ez az osztály kezeli az adatbázis-kapcsolatot.

### Miért kell külön `Database` osztály?

Azért, hogy ne kelljen minden fájlban újra leírni:

- adatbázis host,
- port,
- adatbázisnév,
- felhasználó,
- jelszó,
- PDO beállítások.

Így a projektben mindenhol ugyanazt a kapcsolatot lehet használni.

### Mi az a singleton?

A singleton egy olyan megoldás, ahol egy osztályból csak egy példányt akarunk használni.

Ebben a projektben ez azért jó, mert nem akarunk minden lekérdezéshez új adatbázis-kapcsolatot nyitni.

Fontos rész:

```php
private static $instance = null;
```

Ez tárolja az egyetlen Database példányt.

```php
private function __construct()
```

A konstruktor private, tehát kívülről nem lehet ezt írni:

```php
new Database();
```

Ez direkt van így, mert azt akarjuk, hogy mindenki a `getInstance()` metódust használja.

### Mi az a `getInstance()`?

```php
public static function getInstance()
{
    if (self::$instance === null) {
        self::$instance = new self();
    }

    return self::$instance;
}
```

Jelentése:

- ha még nincs Database példány, létrehoz egyet,
- ha már van, visszaadja a meglévőt.

### Mi az a `self::`?

A `self::` azt jelenti, hogy ugyanennek az osztálynak egy static tulajdonságát vagy metódusát használjuk.

Példa:

```php
self::$instance
```

Ez azt jelenti:

```text
A Database osztály saját $instance változója.
```

Nem egy konkrét objektumhoz tartozik, hanem magához az osztályhoz.

### Mi az a `new self()`?

Ez azt jelenti:

```text
Hozz létre egy új példányt ebből az osztályból.
```

Ebben az esetben:

```php
self::$instance = new self();
```

Ez egy új `Database` objektumot hoz létre, és eltárolja az `$instance` változóban.

### Mi az a `getConnection()`?

```php
public function getConnection()
{
    return $this->connection;
}
```

Ez visszaadja a PDO adatbázis-kapcsolatot.

Példa használat:

```php
$this->db = Database::getInstance()->getConnection();
```

Ez három lépés:

```text
Database::getInstance()
↓
elkéri az egyetlen Database példányt
↓
getConnection()
↓
visszaadja a PDO kapcsolatot
```

## `Game`

Fájl:

```text
classes/Game.php
```

Ez az osztály a játékokkal kapcsolatos adatbázis-műveleteket végzi.

Fontos metódusok:

- `getAll()`: aktív játékok listázása,
- `getBySlug()`: egy játék lekérése URL alapján,
- `getById()`: egy játék lekérése admin szerkesztéshez,
- `create()`: új játék mentése,
- `update()`: meglévő játék módosítása,
- `delete()`: játék logikai törlése.

### Miért van külön Game osztály?

Azért, hogy az SQL kód ne legyen szétszórva minden oldalon.

Így például az `index.php` nem közvetlenül ír SQL-t, hanem ezt mondja:

```php
$games = $gameModel->getAll();
```

Ez sokkal olvashatóbb.

### Mi az a `prepareGameData()`?

Ez előkészíti az űrlapból érkező adatokat.

Például:

- levágja a felesleges szóközöket,
- üres dátumnál `null` értéket ad,
- számmá alakítja az értékelést.

### Mi az a `createSlug()`?

Ez a játék címéből URL-barát szöveget készít.

Példa:

```text
Dark Souls III -> dark-souls-iii
```

Ez azért hasznos, mert a link így olvasható:

```text
game.php?slug=dark-souls-iii
```

## `Review`

Fájl:

```text
classes/Review.php
```

Ez az osztály a recenziókat kezeli.

Fontos metódusok:

- `getByGameId()`: egy játék recenzióinak lekérése,
- `getLatest()`: legfrissebb recenziók lekérése a főoldalra,
- `create()`: új recenzió mentése,
- `updateGameRating()`: átlagértékelés frissítése.

### Hogyan frissül az átlagértékelés?

Amikor új recenzió kerül be, a rendszer kiszámolja az adott játék átlagpontszámát.

Egyszerűen:

```sql
SELECT AVG(score)
FROM reviews
WHERE game_id = ?
```

Ezután az eredmény bekerül a `games.rating` mezőbe.

Ez azért praktikus, mert a főoldalon gyorsan ki lehet írni az értékelést.

## `User`

Fájl:

```text
classes/User.php
```

Ez az osztály a felhasználókat kezeli.

Fontos metódus:

```php
authenticate($username, $password)
```

Ez:

1. megkeresi a felhasználót adatbázisban,
2. ellenőrzi a jelszót,
3. siker esetén visszaadja a felhasználó adatait,
4. hibás jelszónál `null` értéket ad vissza.

### Mi az a `password_verify()`?

A jelszavak nem sima szövegként vannak tárolva.

Amikor belép valaki:

```php
password_verify($password, $user['password'])
```

Ez összehasonlítja a beírt jelszót az adatbázisban lévő hash-sel.

Ez biztonságosabb, mert az adatbázisban nem látszik az eredeti jelszó.

## `Session`

Fájl:

```text
classes/Session.php
```

A session arra való, hogy a PHP megjegyezze, ki van bejelentkezve.

HTTP-ben minden oldalbetöltés külön kérés. Session nélkül a PHP nem tudná, hogy ugyanaz a felhasználó kattintott tovább.

### Fontos metódusok

```php
Session::start();
```

Elindítja a sessiont.

```php
Session::set('user_id', 1);
```

Értéket ment a sessionbe.

```php
Session::get('username');
```

Értéket olvas ki a sessionből.

```php
Session::isLoggedIn();
```

Megnézi, van-e bejelentkezett felhasználó.

```php
Session::isAdmin();
```

Megnézi, admin-e a felhasználó.

```php
Session::requireLogin();
```

Ha nincs belépve, átirányítja a login oldalra.

```php
Session::requireAdmin();
```

Csak adminnak engedi az oldalt.

### Miért van itt is `self::`?

Példa:

```php
return self::get('user_id') !== null;
```

Ez azt jelenti:

```text
Ugyanennek a Session osztálynak a get() metódusát hívjuk meg.
```

Azért `self::`, mert a `get()` metódus static.

## 6. Fontos segédfüggvények

Fájl:

```text
includes/functions.php
```

### `e()`

```php
function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
```

Ez biztonságosan ír ki HTML-be.

Példa:

```php
<?= e($game['title']) ?>
```

Ha valaki rosszindulatú HTML vagy JavaScript kódot írna be, az nem futna le, hanem szövegként jelenne meg.

Ez XSS támadás ellen véd.

### `formatDate()`

Dátumot formáz.

Példa:

```text
2022-02-25 -> 25.02.2022
```

### `renderStars()`

Csillagokat rajzol az értékelés alapján.

Példa:

```text
★★★★★★★★★☆
```

### `cleanText()`

Űrlapból érkező szöveget tisztít.

Legfontosabb része:

```php
trim()
```

Ez levágja a felesleges szóközöket a szöveg elejéről és végéről.

### `collectGameFormData()`

Összegyűjti az admin játékűrlap mezőit egy tömbbe.

Ez azért jó, mert a létrehozás és szerkesztés ugyanazt az adatszerkezetet használja.

### `validateGameData()`

Ellenőrzi az admin játékűrlap adatait.

Például:

- a cím nem lehet üres,
- a kép URL-nek érvényes URL-nek kell lennie,
- az értékelés 0 és 10 között legyen.

## 7. `require_once`, `require` és autoload

### Mi az a `require_once`?

Példa:

```php
require_once __DIR__ . '/includes/functions.php';
```

Ez betölt egy másik PHP fájlt.

A `once` azt jelenti, hogy ugyanazt a fájlt csak egyszer tölti be. Ez azért jó, mert ha kétszer töltenénk be ugyanazt a függvényt vagy osztályt, PHP hiba lehetne.

### Mi az a `require`?

A `require` is betölt egy fájlt, de nincs benne az egyszeri betöltés védelme.

A projektben például a `Database.php` betölti a konfigurációt:

```php
$config = require __DIR__ . '/../config/database.php';
```

Itt a `database.php` egy tömböt ad vissza, például hostot, portot és adatbázisnevet.

### Mi az a `__DIR__`?

Az aktuális fájl mappájának útvonala.

Ez azért jó, mert így a fájlok akkor is megtalálják egymást, ha máshonnan nyitjuk meg az oldalt.

### Mi az az autoload?

Fájl:

```text
vendor/autoload.php
```

Ez a Composer autoload fájlja.

A `composer.json` megmondja, hogy a `classes/` mappában lévő osztályokat automatikusan be lehet tölteni.

Ezért lehet ezt írni:

```php
$gameModel = new Game();
```

anélkül, hogy minden osztályfájlt külön kézzel be kellene tölteni.

## 8. PDO és SQL

### Mi az a PDO?

A PDO a PHP beépített adatbázis-kezelő eszköze.

Ezzel lehet:

- kapcsolódni MySQL-hez,
- SQL lekérdezést futtatni,
- adatokat lekérni,
- adatokat menteni.

### Mi az a DSN?

A DSN mondja meg, hova kapcsolódjon a PDO.

Példa:

```php
mysql:host=127.0.0.1;port=3307;dbname=game_reviews;charset=utf8mb4
```

Ez tartalmazza:

- adatbázis típusát: MySQL,
- hostot,
- portot,
- adatbázis nevét,
- karakterkódolást.

### Mi az a prepared statement?

Példa:

```php
$stmt = $this->db->prepare("
    SELECT *
    FROM games
    WHERE slug = :slug
");
$stmt->execute(['slug' => $slug]);
```

Ez biztonságosabb, mint ha közvetlenül beleraknánk a felhasználói adatot az SQL szövegbe.

Rossz példa:

```php
"SELECT * FROM games WHERE slug = '$slug'"
```

Ez SQL injection veszélyes lehet.

Jó példa:

```php
WHERE slug = :slug
```

Itt a PDO külön kezeli az SQL parancsot és a felhasználói adatot.

## 9. Bejelentkezés folyamata

```text
Felhasználó megnyitja az admin/login.php oldalt
↓
Beírja a felhasználónevet és jelszót
↓
Az űrlap POST kéréssel elküldi az adatokat
↓
User::authenticate() megkeresi a felhasználót
↓
password_verify() ellenőrzi a jelszót
↓
Siker esetén sessionbe kerül a user_id, username és role
↓
A felhasználó átkerül az admin/index.php oldalra
```

Ha rossz a jelszó, hibaüzenet jelenik meg.

## 10. Recenzió írásának folyamata

```text
Felhasználó megnyit egy játékoldalt
↓
Ha nincs bejelentkezve, csak belépési linket lát
↓
Ha be van jelentkezve, megjelenik a recenzió űrlap
↓
Kitölti a címet, szöveget, pontszámot, pluszokat és mínuszokat
↓
game.php ellenőrzi az adatokat
↓
Review::create() elmenti a recenziót
↓
Review::updateGameRating() újraszámolja az átlagot
↓
Az oldal újra megjelenik az új recenzióval
```

## 11. Admin játékkezelés

Admin funkciók:

- játék hozzáadása,
- játék szerkesztése,
- játék törlése.

Ezeket a `Session::requireAdmin()` védi.

Ez azt jelenti:

```text
Ha nem vagy belépve -> login oldal
Ha beléptél, de nem vagy admin -> admin kezdőoldal
Ha admin vagy -> mehetsz tovább
```

## 12. Biztonsági megoldások

### SQL injection elleni védelem

A projekt prepared statementeket használ:

```php
$stmt = $this->db->prepare(...);
$stmt->execute(...);
```

Ez védi az adatbázist a rosszindulatú SQL beszúrástól.

### XSS elleni védelem

A HTML kiírásnál a projekt az `e()` függvényt használja:

```php
<?= e($game['title']) ?>
```

Ez megakadályozza, hogy HTML vagy JavaScript kód fusson le felhasználói adatból.

### Jelszavak védelme

A jelszavak hashként vannak tárolva.

Használt függvények:

```php
password_hash()
password_verify()
```

### Admin oldalak védelme

Admin oldalak elején:

```php
Session::requireAdmin();
```

Ez biztosítja, hogy játékot csak admin tudjon kezelni.

### `setup.php` eltávolítása

A `setup.php` már törölve lett, mert kész projektben nem szükséges.

Ez jó döntés, mert egy publikus setup fájl adatbázist vagy alap felhasználókat módosíthatna.

Telepítéshez az `install.sql` maradt meg.

## 13. Mit jelent az, hogy több `index.php` van?

A projektben több `index.php` van, mert minden mappának lehet saját kezdőoldala.

```text
index.php
```

Főoldal.

```text
admin/index.php
```

Admin kezdőoldal.

```text
admin/games/index.php
```

Admin játéklista.

Ez normális PHP projektekben.

Az `index.php` jelentése:

```text
ennek a mappának az alapoldala
```

## 14. Fontos PHP jelek és kifejezések

### `->`

Objektum metódusának vagy tulajdonságának elérése.

Példa:

```php
$gameModel->getAll();
```

Jelentése:

```text
Hívd meg a $gameModel objektum getAll() metódusát.
```

### `::`

Static metódus vagy static tulajdonság elérése.

Példa:

```php
Session::start();
```

Jelentése:

```text
Hívd meg a Session osztály start() metódusát objektum létrehozása nélkül.
```

### `$this`

Az aktuális objektumra mutat.

Példa:

```php
$this->db
```

Jelentése:

```text
ennek az objektumnak a db tulajdonsága
```

### `self`

Az aktuális osztályra mutat static környezetben.

Példa:

```php
self::get('role')
```

Jelentése:

```text
ugyanennek az osztálynak a get() metódusa
```

### `public`

Bárhonnan elérhető.

### `private`

Csak az adott osztályon belül elérhető.

### `static`

Az osztályhoz tartozik, nem egy konkrét objektumhoz.

Ezért hívható így:

```php
Session::start();
```

## 15. Példa: hogyan magyaráznám el tanárnak?

Rövid bemutató:

> Ez egy PHP és MySQL alapú játékértékelő oldal. A projekt objektumorientált felépítést használ. Az adatbázis-kapcsolatot a Database osztály kezeli PDO-val. A játékokkal kapcsolatos műveletek a Game osztályban vannak, a recenziók a Review osztályban, a bejelentkezés pedig a User és Session osztályokkal működik. Az admin felület külön védve van, csak admin jogosultságú felhasználó tud játékot hozzáadni, módosítani vagy törölni. A felhasználói adatokat prepared statementekkel kezeljük, a HTML kiírást pedig htmlspecialchars védi.

## 16. Mit érdemes külön megtanulni?

Ha szóban kell bemutatni, ezekre készülj:

- Mi a különbség a főoldal, játéklista és játék részletes oldal között?
- Miért van külön `Game`, `Review`, `User`, `Session`, `Database` osztály?
- Mi az a PDO?
- Mi az a prepared statement?
- Mi az a session?
- Mi az a `self::`?
- Mi az a singleton?
- Miért kell a `getInstance()`?
- Mit csinál a `getConnection()`?
- Mi az a slug?
- Miért nem törlünk fizikailag játékot?
- Miért kell `htmlspecialchars()`?
- Hogyan frissül a játék átlagértékelése?

## 17. Hasznos belépési adatok

Admin:

```text
felhasználó: admin
jelszó: admin123
```

Teszt felhasználó:

```text
felhasználó: user
jelszó: user123
```

## 18. Telepítés röviden

1. XAMPP-ban induljon az Apache és a MySQL.
2. A projekt legyen itt:

```text
C:\xampp\htdocs\game-review-php-main
```

3. Az `install.sql` tartalmát futtasd le MySQL-ben vagy phpMyAdminban.
4. Nyisd meg:

```text
http://localhost/game-review-php-main/index.php
```

## 19. Összefoglalás

A projekt lényege:

- PHP kezeli az oldalakat,
- MySQL tárolja az adatokat,
- PDO kapcsolja össze a PHP-t és az adatbázist,
- osztályok választják szét a felelősségeket,
- session kezeli a belépést,
- admin oldal kezeli a játékokat,
- recenziók alapján frissülnek az értékelések.

Ez egy jól magyarázható, iskolai szinthez illő webprojekt, mert a működés minden része külön fájlokban és egyszerű logika szerint van felépítve.
