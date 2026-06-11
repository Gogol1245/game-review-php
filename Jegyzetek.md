# Jegyzetek PHP fogalmakhoz

Ez a fájl rövid tanulási jegyzet a projektben használt fontosabb PHP fogalmakhoz. Úgy van megírva, hogy szóban is könnyen el tudd magyarázni.

## `getInstance()`

A `getInstance()` a `Database` osztályban található.

Feladata:

- visszaadja az adatbázis-kezelő egyetlen közös példányát,
- ha még nincs ilyen példány, létrehozza,
- ha már van, akkor a meglévőt adja vissza.

Ez a singleton minta része.

Egyszerűen:

```text
Ne nyissunk minden fájlban új adatbázis-kapcsolatot, hanem használjunk egy közös példányt.
```

Példa:

```php
Database::getInstance()
```

## `getConnection()`

A `getConnection()` szintén a `Database` osztályban van.

Feladata:

- visszaadja a PDO adatbázis-kapcsolatot,
- ezt használják a `Game`, `Review` és `User` osztályok SQL lekérdezésekhez.

Példa:

```php
$this->db = Database::getInstance()->getConnection();
```

Ez azt jelenti:

```text
Kérem a közös Database példányt, abból pedig kérem a tényleges adatbázis-kapcsolatot.
```

## `require_once`

A `require_once` betölt egy másik PHP fájlt, de csak egyszer.

Példa:

```php
require_once __DIR__ . '/includes/functions.php';
```

Miért jó?

- nem kell minden kódot egy fájlba írni,
- külön fájlokba lehet szervezni a projektet,
- a `once` miatt ugyanaz a fájl nem töltődik be kétszer,
- így elkerülhető például az, hogy ugyanaz a függvény kétszer legyen definiálva.

## `require`

A `require` szintén betölt egy fájlt.

Példa a projektben:

```php
$config = require __DIR__ . '/../config/database.php';
```

Itt a `database.php` egy tömböt ad vissza az adatbázis beállításaival.

Különbség:

- `require`: minden híváskor betölti a fájlt,
- `require_once`: csak egyszer tölti be.

## `__DIR__`

Az `__DIR__` az aktuális fájl mappájának teljes útvonala.

Azért hasznos, mert így biztosan jó helyről töltjük be a fájlokat.

Példa:

```php
__DIR__ . '/vendor/autoload.php'
```

Ez azt jelenti:

```text
Az aktuális mappából keresd meg a vendor/autoload.php fájlt.
```

## `self::`

A `self::` az aktuális osztályra mutat.

Példa:

```php
self::$instance
```

Ez azt jelenti:

```text
Ugyanennek az osztálynak a static $instance változója.
```

Másik példa:

```php
self::get('role')
```

Ez azt jelenti:

```text
Ugyanennek az osztálynak a get() metódusát hívjuk meg.
```

## `new self()`

A `new self()` új objektumot hoz létre abból az osztályból, amelyben éppen vagyunk.

Példa:

```php
self::$instance = new self();
```

Ez a `Database` osztályban azt jelenti:

```text
Hozz létre egy új Database objektumot, és tárold el az $instance változóban.
```

## `$this`

A `$this` az aktuális objektumot jelenti.

Példa:

```php
$this->db
```

Ez azt jelenti:

```text
Ennek az objektumnak a db tulajdonsága.
```

A `Game` osztályban például a `$this->db` az adatbázis-kapcsolatot tárolja.

## `->`

A `->` objektumon belüli tulajdonságot vagy metódust ér el.

Példa:

```php
$gameModel->getAll();
```

Ez azt jelenti:

```text
A $gameModel objektum getAll() metódusát hívjuk meg.
```

## `::`

A `::` static metódus vagy static tulajdonság elérésére szolgál.

Példa:

```php
Session::start();
```

Ez azt jelenti:

```text
A Session osztály start() metódusát hívjuk meg objektum létrehozása nélkül.
```

## `static`

A `static` azt jelenti, hogy valami az osztályhoz tartozik, nem egy konkrét objektumhoz.

Példa:

```php
private static $instance = null;
```

Ez a `Database` osztály közös példányát tárolja.

## `public`

A `public` azt jelenti, hogy a metódus vagy tulajdonság kívülről is elérhető.

Példa:

```php
public function getConnection()
```

Ezt más osztályok is meghívhatják.

## `private`

A `private` azt jelenti, hogy az adott metódus vagy tulajdonság csak az osztályon belül használható.

Példa:

```php
private function __construct()
```

A `Database` osztály konstruktora private, hogy kívülről ne lehessen új példányt létrehozni.

## PDO

A PDO a PHP adatbázis-kezelő eszköze.

Ezzel lehet:

- kapcsolódni MySQL adatbázishoz,
- SQL lekérdezéseket futtatni,
- adatokat lekérni,
- adatokat beszúrni vagy módosítani.

Példa:

```php
new PDO($dsn, $username, $password)
```

## DSN

A DSN mondja meg a PDO-nak, hogy melyik adatbázishoz kapcsolódjon.

Példa:

```php
mysql:host=127.0.0.1;port=3307;dbname=game_reviews;charset=utf8mb4
```

Ez tartalmazza:

- adatbázis típusát,
- szerver címét,
- portot,
- adatbázis nevét,
- karakterkódolást.

## `prepare()`

A `prepare()` előkészít egy SQL lekérdezést.

Példa:

```php
$stmt = $this->db->prepare("SELECT * FROM games WHERE slug = :slug");
```

Ez még nem futtatja le a lekérdezést, csak előkészíti.

## `execute()`

Az `execute()` futtatja le az előkészített SQL lekérdezést.

Példa:

```php
$stmt->execute(['slug' => $slug]);
```

Ez biztonságosan behelyettesíti a `:slug` helyére az értéket.

## Prepared statement

A prepared statement előkészített SQL lekérdezést jelent.

Miért jó?

- biztonságosabb,
- véd SQL injection ellen,
- a felhasználói adat nem kerül közvetlenül az SQL szövegbe.

Rossz példa:

```php
"SELECT * FROM games WHERE slug = '$slug'"
```

Jó példa:

```php
"SELECT * FROM games WHERE slug = :slug"
```

## `fetch()`

A `fetch()` egyetlen sort kér le az SQL eredményből.

Példa:

```php
$game = $stmt->fetch();
```

Ezt akkor használjuk, amikor egy rekordot várunk, például egy játékot slug alapján.

## `fetchAll()`

A `fetchAll()` az összes eredményt lekéri.

Példa:

```php
$games = $stmt->fetchAll();
```

Ezt akkor használjuk, amikor több rekordot várunk, például játéklistát.

## `password_hash()`

A `password_hash()` biztonságos jelszó hash-t készít.

Ez azért kell, hogy az adatbázisban ne sima szövegként legyen a jelszó.

## `password_verify()`

A `password_verify()` ellenőrzi, hogy a beírt jelszó egyezik-e az adatbázisban tárolt hash-sel.

Példa:

```php
password_verify($password, $user['password'])
```

## Session

A session arra való, hogy a PHP megjegyezze, ki van bejelentkezve.

Példa:

```php
Session::set('user_id', $authenticatedUser['id']);
```

Ez elmenti a felhasználó azonosítóját a sessionbe.

## `header('Location: ...')`

Ez átirányítja a böngészőt egy másik oldalra.

Példa:

```php
header('Location: /game-review-php-main/admin/login.php');
exit;
```

Az `exit` azért kell utána, hogy a PHP ne fusson tovább.

## `htmlspecialchars()`

Ez biztonságosan ír ki szöveget HTML-be.

A projektben az `e()` függvény használja.

Ez véd XSS támadás ellen, mert a beírt HTML vagy JavaScript nem fut le.

## `trim()`

A `trim()` levágja a felesleges szóközöket a szöveg elejéről és végéről.

Példa:

```php
trim($_POST['title'])
```

## `$_GET`

Az URL-ből érkező adatokat tartalmazza.

Példa:

```php
game.php?slug=minecraft
```

Itt:

```php
$_GET['slug']
```

értéke `minecraft`.

## `$_POST`

Űrlapból érkező adatokat tartalmaz.

Példa:

```php
$_POST['username']
```

Ezt a login űrlap használja.

## `$_SESSION`

A sessionben tárolt adatokat tartalmazza.

Példa:

```php
$_SESSION['user_id']
```

Ez alapján tudja a rendszer, hogy a felhasználó be van jelentkezve.

## Slug

A slug URL-barát név.

Példa:

```text
God of War -> god-of-war
```

Ez azért jó, mert a link olvashatóbb:

```text
game.php?slug=god-of-war
```

## Logikai törlés

Logikai törlésnél nem töröljük ki fizikailag a sort az adatbázisból.

Ehelyett:

```sql
is_active = 0
```

Így a játék eltűnik az oldalról, de az adatbázisban megmarad.

## `is_active`

Ez jelzi, hogy egy játék aktív-e.

- `1`: aktív, megjelenik az oldalon,
- `0`: inaktív, nem jelenik meg.

## `is_published`

Ez jelzi, hogy egy recenzió publikus-e.

- `1`: látható,
- `0`: nem látható.

## `renderGameCard()`

Ez egy saját segédfüggvény.

Feladata:

- kirajzol egy játék kártyát,
- képpel,
- címmel,
- műfajjal,
- platformmal,
- értékeléssel.

Azért hasznos, mert az `index.php` és a `games.php` is ugyanazt a kártyát használja, így nem kell kétszer ugyanazt a HTML-t leírni.

## `collectGameFormData()`

Ez összegyűjti az admin játékűrlap mezőit egy tömbbe.

Így a `create.php` és az `edit.php` ugyanazt a szerkezetet használja.

## `validateGameData()`

Ez ellenőrzi a játék űrlap adatait.

Például:

- a cím ne legyen üres,
- az értékelés 0 és 10 között legyen,
- a kép URL érvényes legyen.

## Rövid felelős mondat

Ha nagyon röviden kell elmagyarázni:

```text
A projekt objektumorientált PHP-t használ. Az adatbázis-kapcsolatot a Database osztály kezeli PDO-val, a játékokat a Game osztály, a recenziókat a Review osztály, a belépést pedig a User és Session osztályok. A prepared statement védi az SQL lekérdezéseket, az e() függvény pedig a HTML kiírást.
```
