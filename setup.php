<?php
// Teljes telepítő és adatbázis-előkészítő fájl.
// Ezt akkor érdemes futtatni, ha a projektet új gépen vagy üres adatbázissal indítjuk.
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🎮 Game Reviews - Complete Setup</h1>";

// Alap játékadatok.
// A slug egyedi azonosítóként is működik, ezért INSERT helyett később upsertet használunk.
$seedGames = [
    [
        'title' => 'GTA V',
        'slug' => 'gta-v',
        'description' => 'Grand Theft Auto V egy nyílt világú akciójáték, amely Los Santos városában és környékén játszódik. A játék három főszereplő történetét követi, miközben rablások, küldetések, vezetés, lövöldözés és szabad felfedezés váltják egymást. A részletes világ, a sok melléktevékenység és az online mód miatt hosszú ideig újrajátszható.',
        'developer' => 'Rockstar North',
        'publisher' => 'Rockstar Games',
        'release_date' => '2013-09-17',
        'genre' => 'Akčná adventúra',
        'platform' => 'PC, PlayStation, Xbox',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/271590/header.jpg',
        'rating' => 9.5,
    ],
    [
        'title' => 'Red Dead Redemption 2',
        'slug' => 'red-dead-redemption-2',
        'description' => 'Nyílt világú western kalandjáték Arthur Morgan történetével, erős hangulattal és részletes világgal.',
        'developer' => 'Rockstar Studios',
        'publisher' => 'Rockstar Games',
        'release_date' => '2018-10-26',
        'genre' => 'Akčná adventúra',
        'platform' => 'PC, PlayStation, Xbox',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/1174180/header.jpg',
        'rating' => 9.7,
    ],
    [
        'title' => 'God of War',
        'slug' => 'god-of-war',
        'description' => 'Kratos és Atreus történetközpontú akciókalandja látványos harcokkal és erős karakterfejlődéssel.',
        'developer' => 'Santa Monica Studio',
        'publisher' => 'PlayStation Publishing LLC',
        'release_date' => '2018-04-20',
        'genre' => 'Akčná adventúra',
        'platform' => 'PC, PlayStation',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/1593500/header.jpg',
        'rating' => 9.4,
    ],
    [
        'title' => 'Hades',
        'slug' => 'hades',
        'description' => 'Gyors tempójú roguelike akciójáték, amelyben Zagreus próbál kijutni az alvilágból.',
        'developer' => 'Supergiant Games',
        'publisher' => 'Supergiant Games',
        'release_date' => '2020-09-17',
        'genre' => 'Roguelike',
        'platform' => 'PC, PlayStation, Xbox, Nintendo Switch',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/1145360/header.jpg',
        'rating' => 9.3,
    ],
    [
        'title' => 'Minecraft',
        'slug' => 'minecraft',
        'description' => 'Kreatív sandbox játék, ahol építeni, bányászni, túlélni és saját világokat létrehozni lehet.',
        'developer' => 'Mojang Studios',
        'publisher' => 'Mojang Studios',
        'release_date' => '2011-11-18',
        'genre' => 'Sandbox',
        'platform' => 'PC, mobil, konzol',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/en/b/b6/Minecraft_2024_cover_art.png',
        'rating' => 9.6,
    ],
    [
        'title' => 'Baldur\'s Gate 3',
        'slug' => 'baldurs-gate-3',
        'description' => 'Körökre osztott fantasy RPG döntésekkel, társakkal és sokféle történeti lehetőséggel.',
        'developer' => 'Larian Studios',
        'publisher' => 'Larian Studios',
        'release_date' => '2023-08-03',
        'genre' => 'RPG',
        'platform' => 'PC, PlayStation, Xbox',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/1086940/header.jpg',
        'rating' => 9.8,
    ],
    [
        'title' => 'Hollow Knight',
        'slug' => 'hollow-knight',
        'description' => 'Hangulatos metroidvania, amely kihívást jelentő harccal és felfedezéssel építi fel Hallownest világát.',
        'developer' => 'Team Cherry',
        'publisher' => 'Team Cherry',
        'release_date' => '2017-02-24',
        'genre' => 'Metroidvania',
        'platform' => 'PC, PlayStation, Xbox, Nintendo Switch',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/367520/header.jpg',
        'rating' => 9.2,
    ],
    [
        'title' => 'Stardew Valley',
        'slug' => 'stardew-valley',
        'description' => 'Nyugodt farmos szerepjáték, ahol termeszteni, bányászni, horgászni és kapcsolatokat építeni lehet.',
        'developer' => 'ConcernedApe',
        'publisher' => 'ConcernedApe',
        'release_date' => '2016-02-26',
        'genre' => 'Simulácia',
        'platform' => 'PC, mobil, konzol',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/413150/header.jpg',
        'rating' => 9.1,
    ],
    [
        'title' => 'Portal 2',
        'slug' => 'portal-2',
        'description' => 'Logikai puzzle játék portálfegyverrel, humoros történettel és kiváló pályatervezéssel.',
        'developer' => 'Valve',
        'publisher' => 'Valve',
        'release_date' => '2011-04-18',
        'genre' => 'Puzzle',
        'platform' => 'PC, PlayStation, Xbox',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/620/header.jpg',
        'rating' => 9.5,
    ],
    [
        'title' => 'DOOM Eternal',
        'slug' => 'doom-eternal',
        'description' => 'Gyors, intenzív FPS, amely agresszív harcot, pontos mozgást és látványos arénákat kínál.',
        'developer' => 'id Software',
        'publisher' => 'Bethesda Softworks',
        'release_date' => '2020-03-20',
        'genre' => 'FPS',
        'platform' => 'PC, PlayStation, Xbox, Nintendo Switch',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/782330/header.jpg',
        'rating' => 9.0,
    ],
    [
        'title' => 'The Last of Us Part I',
        'slug' => 'the-last-of-us-part-i',
        'description' => 'Történetközpontú túlélő akciójáték Joel és Ellie útjáról egy veszélyes világban.',
        'developer' => 'Naughty Dog',
        'publisher' => 'PlayStation Publishing LLC',
        'release_date' => '2022-09-02',
        'genre' => 'Akčná adventúra',
        'platform' => 'PC, PlayStation',
        'image_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/1888930/header.jpg',
        'rating' => 9.0,
    ],
];

try {
    // Először adatbázisnév nélkül kapcsolódunk, hogy létre tudjuk hozni a game_reviews adatbázist.
    $pdo = new PDO("mysql:host=127.0.0.1;port=3307;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "<p style='color:green'>✅ Connected to MySQL</p>";

    // Adatbázis létrehozása, ha még nem létezik.
    $pdo->exec("CREATE DATABASE IF NOT EXISTS game_reviews CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color:green'>✅ Database ready</p>";

    $pdo->exec("USE game_reviews");

    // Users tábla: a bejelentkezéshez szükséges felhasználókat tárolja.
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'editor') DEFAULT 'editor',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>✅ Users table ready</p>";

    // Games tábla: a weboldalon megjelenő játékadatokat tárolja.
    $pdo->exec("CREATE TABLE IF NOT EXISTS games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        description TEXT,
        developer VARCHAR(100),
        publisher VARCHAR(100),
        release_date DATE,
        genre VARCHAR(100),
        platform VARCHAR(100),
        image_url VARCHAR(255),
        rating DECIMAL(3,1) DEFAULT 0.0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_slug (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>✅ Games table ready</p>";

    // Reviews tábla: a játékokhoz tartozó felhasználói értékeléseket tárolja.
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT NOT NULL,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        score INT NOT NULL,
        pros TEXT,
        cons TEXT,
        is_published TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>✅ Reviews table ready</p>";

    // Alap felhasználók létrehozása vagy frissítése.
    $userStatement = $pdo->prepare("
        INSERT INTO users (username, email, password, role)
        VALUES (:username, :email, :password, :role)
        ON DUPLICATE KEY UPDATE email = VALUES(email), password = VALUES(password), role = VALUES(role)
    ");
    $userStatement->execute([
        'username' => 'admin',
        'email' => 'admin@gamereviews.sk',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin',
    ]);
    $userStatement->execute([
        'username' => 'user',
        'email' => 'user@gamereviews.sk',
        'password' => password_hash('user123', PASSWORD_DEFAULT),
        'role' => 'editor',
    ]);
    echo "<p style='color:green'>✅ Users ready (admin/admin123, user/user123)</p>";

    // Játékok beszúrása vagy frissítése slug alapján.
    $gameStatement = $pdo->prepare("
        INSERT INTO games (title, slug, description, developer, publisher, release_date, genre, platform, image_url, rating, is_active)
        VALUES (:title, :slug, :description, :developer, :publisher, :release_date, :genre, :platform, :image_url, :rating, 1)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            description = VALUES(description),
            developer = VALUES(developer),
            publisher = VALUES(publisher),
            release_date = VALUES(release_date),
            genre = VALUES(genre),
            platform = VALUES(platform),
            image_url = VALUES(image_url),
            rating = VALUES(rating),
            is_active = 1
    ");

    foreach ($seedGames as $game) {
        $gameStatement->execute($game);
    }
    echo "<p style='color:green'>✅ Seed games inserted or updated</p>";

    echo "<h2 style='color:green; margin-top:30px;'>✅ SETUP COMPLETE!</h2>";
    echo "<div style='background:#e8f5e9; padding:20px; border-radius:8px; margin:20px 0;'>";
    echo "<h3>Login Information:</h3>";
    echo "<p><strong>Admin:</strong> admin / admin123</p>";
    echo "<p><strong>User:</strong> user / user123</p>";
    echo "</div>";
    echo "<p><a href='index.php' style='font-size:18px; padding:10px 20px; background:#e94560; color:white; text-decoration:none; border-radius:5px;'>🎮 GO TO WEBSITE</a></p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
