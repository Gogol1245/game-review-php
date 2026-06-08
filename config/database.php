<?php

// Ez a fájl csak az adatbázis-kapcsolathoz szükséges beállításokat adja vissza.
// A Database osztály require segítségével tölti be, így a kapcsolódási adatok egy helyen maradnak.
return [
    // A MySQL szerver címe. XAMPP alatt a 127.0.0.1 megbízhatóbb lehet, mint a localhost.
    'host' => '127.0.0.1',

    // A helyi MySQL portja. Ebben a projektben a MySQL a 3307-es porton fut.
    'port' => 3307,

    // Az adatbázis neve, amelyben a felhasználók, játékok és recenziók vannak.
    'dbname' => 'game_reviews',

    // XAMPP alapértelmezett fejlesztői felhasználója.
    'username' => 'root',
    'password' => '',

    // Az utf8mb4 miatt az ékezetes betűk és speciális karakterek is helyesen tárolódnak.
    'charset' => 'utf8mb4'
];
