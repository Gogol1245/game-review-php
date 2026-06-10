<?php
// A Database osztály feladata az adatbázis-kapcsolat létrehozása és újrahasználata.
// A projekt minden adatbázis-művelete ezen keresztül kap PDO kapcsolatot.
class Database
{
    // A singleton példányt tárolja.
    // A static azt jelenti, hogy az érték az osztályhoz tartozik, nem egy külön objektumhoz.
    private static $instance = null;

    // Ebben van a tényleges PDO kapcsolat.
    private $connection;

    // A konstruktor private, ezért kívülről nem lehet new Database() módon példányosítani.
    // Ez biztosítja, hogy a getInstance() mindig ugyanazt az egy kapcsolatkezelőt adja vissza.
    private function __construct()
    {
        // A require betölti a külön konfigurációs fájlt.
        // Így nem kell minden osztályban külön leírni az adatbázis nevét, portját és jelszavát.
        $config = require __DIR__ . '/../config/database.php';

        try {
            $port = $config['port'] ? ";port={$config['port']}" : '';
            $charset = $config['charset'] ?? 'utf8mb4';
            $dsn = "mysql:host={$config['host']}{$port};dbname={$config['dbname']};charset={$charset}";

            // A PDO egy beépített PHP adatbázis-kezelő.
            // Prepared statementeket támogat, ezért biztonságosabban kezelhetők vele az SQL lekérdezések.
            $this->connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // A részletes adatbázis-hibát naplózzuk, de nem írjuk ki a látogatónak.
            // Így a hiba fejlesztéskor visszakereshető, de a weboldal nem árul el belső technikai adatokat.
            error_log('Database connection failed: ' . $e->getMessage());
            die('Nepodarilo sa pripojiť k databáze. Skúste to prosím neskôr.');
        }
    }

    // Singleton getInstance().
    // A self:: azt jelenti, hogy ugyanennek az osztálynak a static tulajdonságát vagy metódusát érjük el.
    // Ha még nincs példány, létrehozza; ha már van, a meglévőt adja vissza.
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // Visszaadja a PDO kapcsolatot.
    // Ezt használják a Game, Review és User osztályok az SQL parancsok futtatásához.
    public function getConnection()
    {
        return $this->connection;
    }
}
