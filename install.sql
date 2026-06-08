-- Adatbázis: game_reviews
-- Ez az SQL fájl kézzel is lefuttatható, ha új gépen kell létrehozni az adatbázist.

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
-- A hash érték példa az admin123 jelszóhoz. Kész projektben a belépéshez elegendő az adatbázisban tárolt hash.
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@gamereviews.sk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE email = VALUES(email), role = VALUES(role);

-- Alap és bővített játékadatok.
-- ON DUPLICATE KEY UPDATE miatt a slug alapján meglévő játék frissül, az új játék pedig beszúródik.
INSERT INTO games (title, slug, description, developer, publisher, release_date, genre, platform, image_url, rating, is_active) VALUES
('Elden Ring', 'elden-ring', 'Rozsiahle akčné RPG od FromSoftware, ktoré prenáša soulslike princípy do otvoreného sveta plného tajomstiev, dungeonov a náročných bossov.', 'FromSoftware', 'Bandai Namco Entertainment', '2022-02-25', 'Akčné RPG', 'PC, PlayStation, Xbox', 'https://cdn.cloudflare.steamstatic.com/steam/apps/1245620/header.jpg', 9.7, 1),
('Dark Souls Remastered', 'dark-souls-remastered', 'Remastrovaná verzia legendárneho Dark Souls. Ponúka hutnú atmosféru, prepojený svet Lordranu a výzvy, ktoré preveria trpezlivosť aj postreh.', 'FromSoftware', 'Bandai Namco Entertainment', '2018-05-24', 'Akčné RPG', 'PC, PlayStation, Xbox, Nintendo Switch', 'https://cdn.cloudflare.steamstatic.com/steam/apps/570940/header.jpg', 9.0, 1),
('Dark Souls II: Scholar of the First Sin', 'dark-souls-ii-scholar-of-the-first-sin', 'Rozšírená edícia Dark Souls II s upraveným rozmiestnením nepriateľov, kompletnými DLC a veľkou slobodou pri tvorbe postavy.', 'FromSoftware', 'Bandai Namco Entertainment', '2015-04-01', 'Akčné RPG', 'PC, PlayStation, Xbox', 'https://cdn.cloudflare.steamstatic.com/steam/apps/335300/header.jpg', 8.0, 1),
('Dark Souls III', 'dark-souls-iii', 'Rýchlejšie a veľkolepejšie pokračovanie série, ktoré prináša silné boss súboje, temnú fantasy atmosféru a dôstojné ukončenie trilógie.', 'FromSoftware', 'Bandai Namco Entertainment', '2016-04-12', 'Akčné RPG', 'PC, PlayStation, Xbox', 'https://cdn.cloudflare.steamstatic.com/steam/apps/374320/header.jpg', 9.0, 1),
('Sekiro: Shadows Die Twice', 'sekiro-shadows-die-twice', 'Precízna akčná adventúra postavená na odrážaní útokov, rytme súbojov a samurajskej atmosfére feudálneho Japonska.', 'FromSoftware', 'Activision', '2019-03-22', 'Akčná adventúra', 'PC, PlayStation, Xbox', 'https://cdn.cloudflare.steamstatic.com/steam/apps/814380/header.jpg', 9.7, 1),
('Bloodborne', 'bloodborne', 'Gotický horor a agresívny súbojový systém v jednom z najvýraznejších svetov od FromSoftware. Yharnam je temný, desivý a nezabudnuteľný.', 'FromSoftware', 'Sony Computer Entertainment', '2015-03-24', 'Akčné RPG', 'PlayStation 4', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/68/Bloodborne_Cover_Wallpaper.jpg/500px-Bloodborne_Cover_Wallpaper.jpg', 9.7, 1),
('Demon''s Souls', 'demons-souls', 'Moderný remake hry, ktorá položila základy soulslike žánru. Spája temnú atmosféru, taktické súboje a krásne prepracované lokácie.', 'Bluepoint Games / FromSoftware', 'Sony Interactive Entertainment', '2020-11-12', 'Akčné RPG', 'PlayStation 5', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/11/Demons_Souls_remake_cover_art.jpg/500px-Demons_Souls_remake_cover_art.jpg', 8.0, 1),
('Cyberpunk 2077', 'cyberpunk-2077', 'Príbehové RPG zasadené do Night City, plné výrazných postáv, rozhodnutí, neónovej atmosféry a množstva vedľajších úloh.', 'CD Projekt Red', 'CD Projekt', '2020-12-10', 'RPG', 'PC, PlayStation, Xbox', 'https://cdn.cloudflare.steamstatic.com/steam/apps/1091500/header.jpg', 9.0, 1),
('The Witcher 3: Wild Hunt', 'the-witcher-3-wild-hunt', 'Geraltovo veľké dobrodružstvo v otvorenom svete. Hra vyniká výbornými úlohami, silnými postavami a mimoriadne bohatým RPG obsahom.', 'CD Projekt Red', 'CD Projekt', '2015-05-19', 'RPG', 'PC, PlayStation, Xbox, Nintendo Switch', 'https://cdn.cloudflare.steamstatic.com/steam/apps/292030/header.jpg', 10.0, 1),
('The Witcher 2: Assassins of Kings', 'the-witcher-2-assassins-of-kings', 'Temnejšie fantasy RPG s politickým príbehom, výraznými voľbami a hutnejšou štruktúrou než neskorší tretí diel.', 'CD Projekt Red', 'CD Projekt', '2011-05-17', 'RPG', 'PC, Xbox 360', 'https://cdn.cloudflare.steamstatic.com/steam/apps/20920/header.jpg', 8.0, 1),
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

-- Ukážkoví autori recenzií.
-- Sú to bežní editori, aby recenzie nepôsobili ako anonymné systémové dáta.
INSERT INTO users (username, email, password, role) VALUES
('marek', 'marek@gamereviews.sk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editor'),
('lucia', 'lucia@gamereviews.sk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editor'),
('peter', 'peter@gamereviews.sk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editor'),
('simona', 'simona@gamereviews.sk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editor')
ON DUPLICATE KEY UPDATE email = VALUES(email), role = VALUES(role);

-- Ku každej aktívnej hre vložíme tri slovenské recenzie.
-- NOT EXISTS zabráni tomu, aby sa rovnaké ukážkové recenzie vložili dvakrát.
INSERT INTO reviews (game_id, user_id, title, content, score, pros, cons, is_published)
SELECT
    g.id,
    u.id,
    seed.title,
    REPLACE(seed.content, '{game}', g.title),
    LEAST(10, GREATEST(1, ROUND(g.rating + seed.score_offset))),
    seed.pros,
    seed.cons,
    1
FROM games g
JOIN (
    SELECT 'marek' AS username, 'Výborný herný zážitok' AS title, '{game} ma veľmi bavilo. Hra má dobré tempo, kvalitné spracovanie a dostatok obsahu, takže sa k nej dá ľahko vracať.' AS content, 0 AS score_offset, 'atmosféra, hrateľnosť, obsah' AS pros, 'niektoré časti môžu byť náročnejšie' AS cons
    UNION ALL
    SELECT 'lucia', 'Silná atmosféra a dobré spracovanie', 'Na hre {game} oceňujem hlavne atmosféru a spôsob, akým drží hráča pri hraní. Nie je to len pekná hra, ale aj zábavná.', -1, 'atmosféra, vizuál, zábavnosť', 'miestami pomalšie pasáže'
    UNION ALL
    SELECT 'peter', 'Stojí za zahranie', '{game} je titul, ktorý by si mal vyskúšať každý fanúšik žánru. Má svoje drobné slabiny, ale celkový dojem je veľmi dobrý.', 0, 'kvalitný obsah, dobrý dizajn, znovuhrateľnosť', 'nie všetko sadne každému hráčovi'
) seed
JOIN users u ON u.username = seed.username
WHERE g.is_active = 1
AND NOT EXISTS (
    SELECT 1
    FROM reviews r
    WHERE r.game_id = g.id AND r.title = seed.title
);

-- Po vložení recenzií prepočítame priemerné hodnotenie každej hry.
UPDATE games g
SET rating = (
    SELECT ROUND(AVG(r.score), 1)
    FROM reviews r
    WHERE r.game_id = g.id AND r.is_published = 1
)
WHERE g.is_active = 1;
