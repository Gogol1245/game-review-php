<?php

// HTML kimenet biztonságos kiírása.
// Az e() rövid név, mert sablonokban sokszor használjuk.
// Megakadályozza, hogy egy felhasználó HTML vagy JavaScript kódot írjon ki az oldalra.
function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Dátum formázása emberi olvasásra.
// Ha rossz vagy üres dátum érkezik, inkább üres szöveget adunk vissza, nem hibát.
function formatDate($date, $format = 'd.m.Y')
{
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '';
    }
    return date($format, $timestamp);
}

// 1-10 közötti értékelés csillagos megjelenítése.
// A teli csillagok a pontszámot, az üres csillagok a hiányzó pontokat jelzik.
function renderStars($rating)
{
    $stars = '';
    for ($i = 1; $i <= 10; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';
    }
    return $stars;
}

// URL ellenőrzése képekhez és űrlapokhoz.
// Az üres URL megengedett, mert nem minden játékhoz kötelező képet megadni.
function isValidUrl($url)
{
    if (trim($url) === '') {
        return true;
    }

    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Egyszerű szöveges mezők tisztítása űrlapfeldolgozás előtt.
// A trim() levágja a felesleges szóközöket, így tisztább adat kerül az adatbázisba.
function cleanText($value)
{
    return trim((string)($value ?? ''));
}

// Játék űrlap adatainak összegyűjtése egységes szerkezetbe.
// Így a létrehozás és a szerkesztés ugyanazokat a mezőneveket használja.
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

// Játék űrlap validációja.
// A kötelező cím mellett az URL-t ellenőrizzük, mert rossz kép URL hibás megjelenést okozhat.
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
