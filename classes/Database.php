<?php

// Kozos PDO adatbazis-kapcsolat.
class Database
{
    private static $instance = null;
    private $connection;

    // A private konstruktor miatt a kapcsolat singletonkent mukodik.
    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';

        try {
            $port = $config['port'] ? ";port={$config['port']}" : '';
            $charset = $config['charset'] ?? 'utf8mb4';
            $dsn = "mysql:host={$config['host']}{$port};dbname={$config['dbname']};charset={$charset}";

            $this->connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('Nepodarilo sa pripojiť k databáze. Skúste to prosím neskôr.');
        }
    }

    // Visszaadja az egyetlen kozos Database peldanyt.
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // A modellek ezt a PDO kapcsolatot hasznaljak a prepared SQL lekerdezesekhez.
    public function getConnection()
    {
        return $this->connection;
    }
}
