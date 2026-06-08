-- Adatbázis: game_reviews
-- Ez az SQL fájl kézzel is lefuttatható, ha nem a setup.php telepítőt használjuk.

CREATE DATABASE IF NOT EXISTS game_reviews CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE game_reviews;

-- Felhasználók táblája.
-- Itt tároljuk a bejelentkezéshez szükséges adatokat és a jogosultsági szerepkört.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Játékok táblája.
-- A slug a szép URL-hez kell, a rating pedig a gyors megjelenítés miatt van külön mezőben.
CREATE TABLE IF NOT EXISTS games (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Recenziók táblája.
-- A game_id és user_id idegen kulcs, ezek kapcsolják össze a recenziót a játékkal és a szerzővel.
CREATE TABLE IF NOT EXISTS reviews (
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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_game (game_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alap felhasználók.
-- A hash értékek példák; a setup.php mindig friss, password_hash() által készített hasht generál.
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@gamereviews.sk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE email = VALUES(email), role = VALUES(role);

-- Alap és bővített játékadatok.
-- ON DUPLICATE KEY UPDATE miatt a slug alapján meglévő játék frissül, az új játék pedig beszúródik.
INSERT INTO games (title, slug, description, developer, publisher, release_date, genre, platform, image_url, rating, is_active) VALUES
('GTA V', 'gta-v', 'Grand Theft Auto V egy nyílt világú akciójáték Los Santos városában. Három főszereplő történetét követi, sok küldetéssel, vezetéssel, lövöldözéssel és szabad felfedezéssel.', 'Rockstar North', 'Rockstar Games', '2013-09-17', 'Akčná adventúra', 'PC, PlayStation, Xbox', 'https://cdn.cloudflare.steamstatic.com/steam/apps/271590/header.jpg', 9.5, 1),
('Red Dead Redemption 2', 'red-dead-redemption-2', 'Nyílt világú western kalandjáték erős történettel és részletes világgal.', 'Rockstar Studios', 'Rockstar Games', '2018-10-26', 'Akčná adventúra', 'PC, PlayStation, Xbox', 'https://cdn.cloudflare.steamstatic.com/steam/apps/1174180/header.jpg', 9.7, 1),
('God of War', 'god-of-war', 'Történetközpontú akciókaland Kratos és Atreus útjáról.', 'Santa Monica Studio', 'PlayStation Publishing LLC', '2018-04-20', 'Akčná adventúra', 'PC, PlayStation', 'https://cdn.cloudflare.steamstatic.com/steam/apps/1593500/header.jpg', 9.4, 1),
('Hades', 'hades', 'Gyors tempójú roguelike akciójáték az alvilágból való kijutásról.', 'Supergiant Games', 'Supergiant Games', '2020-09-17', 'Roguelike', 'PC, PlayStation, Xbox, Nintendo Switch', 'https://cdn.cloudflare.steamstatic.com/steam/apps/1145360/header.jpg', 9.3, 1),
('Minecraft', 'minecraft', 'Kreatív sandbox játék építéssel, túléléssel és felfedezéssel.', 'Mojang Studios', 'Mojang Studios', '2011-11-18', 'Sandbox', 'PC, mobil, konzol', 'https://upload.wikimedia.org/wikipedia/en/b/b6/Minecraft_2024_cover_art.png', 9.6, 1),
('Baldur''s Gate 3', 'baldurs-gate-3', 'Körökre osztott fantasy RPG sok döntéssel és társakkal.', 'Larian Studios', 'Larian Studios', '2023-08-03', 'RPG', 'PC, PlayStation, Xbox', 'https://cdn.cloudflare.steamstatic.com/steam/apps/1086940/header.jpg', 9.8, 1),
('Hollow Knight', 'hollow-knight', 'Hangulatos metroidvania kihívást jelentő harccal és felfedezéssel.', 'Team Cherry', 'Team Cherry', '2017-02-24', 'Metroidvania', 'PC, PlayStation, Xbox, Nintendo Switch', 'https://cdn.cloudflare.steamstatic.com/steam/apps/367520/header.jpg', 9.2, 1),
('Stardew Valley', 'stardew-valley', 'Nyugodt farmos szerepjáték termeléssel, bányászattal és kapcsolatokkal.', 'ConcernedApe', 'ConcernedApe', '2016-02-26', 'Simulácia', 'PC, mobil, konzol', 'https://cdn.cloudflare.steamstatic.com/steam/apps/413150/header.jpg', 9.1, 1),
('Portal 2', 'portal-2', 'Logikai puzzle játék portálfegyverrel és humoros történettel.', 'Valve', 'Valve', '2011-04-18', 'Puzzle', 'PC, PlayStation, Xbox', 'https://cdn.cloudflare.steamstatic.com/steam/apps/620/header.jpg', 9.5, 1),
('DOOM Eternal', 'doom-eternal', 'Gyors és intenzív FPS látványos arénaharcokkal.', 'id Software', 'Bethesda Softworks', '2020-03-20', 'FPS', 'PC, PlayStation, Xbox, Nintendo Switch', 'https://cdn.cloudflare.steamstatic.com/steam/apps/782330/header.jpg', 9.0, 1),
('The Last of Us Part I', 'the-last-of-us-part-i', 'Történetközpontú túlélő akciójáték Joel és Ellie útjáról.', 'Naughty Dog', 'PlayStation Publishing LLC', '2022-09-02', 'Akčná adventúra', 'PC, PlayStation', 'https://cdn.cloudflare.steamstatic.com/steam/apps/1888930/header.jpg', 9.0, 1)
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
    is_active = 1;
