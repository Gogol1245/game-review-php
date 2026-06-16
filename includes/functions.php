<?php

// HTML-be kiiras elott biztonsagosan escapeli az ertekeket.
function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Az adatbazisbol jovo datumokat szlovak oldalformara alakitja.
function formatDate($date, $format = 'd.m.Y')
{
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '';
    }
    return date($format, $timestamp);
}

// Az 1-10 kozotti ertekelest teli es ures csillagokkal jeleniti meg.
function renderStars($rating)
{
    $stars = '';
    for ($i = 1; $i <= 10; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';
    }
    return $stars;
}

// Ures kep URL megengedett, de ha van ertek, annak ervenyes URL-nek kell lennie.
function isValidUrl($url)
{
    if (trim($url) === '') {
        return true;
    }

    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Egyszeru szoveges urlapmezoket tisztit mentes elott.
function cleanText($value)
{
    return trim((string)($value ?? ''));
}

// A jatek letrehozo es szerkeszto urlap ugyanazt az adatszerkezetet hasznalja.
function collectGameFormData($source)
{
    return [
        'title' => cleanText($source['title'] ?? ''),
        'description' => cleanText($source['description'] ?? ''),
        'developer' => cleanText($source['developer'] ?? ''),
        'publisher' => cleanText($source['publisher'] ?? ''),
        'release_date' => cleanText($source['release_date'] ?? ''),
        'genre' => cleanText($source['genre'] ?? ''),
        'platform' => cleanText($source['platform'] ?? ''),
        'image_url' => cleanText($source['image_url'] ?? ''),
    ];
}

// Admin jatekurlap ellenorzese Game::create() vagy Game::update() elott.
function validateGameData($data)
{
    $errors = [];

    if ($data['title'] === '') {
        $errors[] = 'Názov hry je povinný.';
    }

    if (!isValidUrl($data['image_url'])) {
        $errors[] = 'URL obrázka musí byť platná adresa.';
    }

    $rating = (float)($data['rating'] ?? 0);
    if ($rating < 0 || $rating > 10) {
        $errors[] = 'Hodnotenie musí byť medzi 0 a 10.';
    }

    return $errors;
}

// Kozos jatekkartya, ezt hasznalja az index.php es a games.php is.
function renderGameCard($game)
{
    ?>
    <div class="game-card">
        <?php if (!empty($game['image_url'])): ?>
            <img src="<?= e($game['image_url']) ?>" alt="<?= e($game['title']) ?>">
        <?php else: ?>
            <div style="height:200px; background:#1a1a2e; display:flex; align-items:center; justify-content:center; color:white; font-size:48px;">🎮</div>
        <?php endif; ?>

        <div class="game-card-content">
            <h3>
                <a href="/game-review-php-main/game.php?slug=<?= e($game['slug']) ?>">
                    <?= e($game['title']) ?>
                </a>
            </h3>
            <p><strong>Žáner:</strong> <?= e($game['genre']) ?></p>
            <p><strong>Platforma:</strong> <?= e($game['platform']) ?></p>
            <?php if ($game['rating'] > 0): ?>
                <p class="rating">Hodnotenie: <?= e($game['rating']) ?>/10</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
