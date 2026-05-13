<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Setup</h1>";

try {
    // Connect without database first
    $pdo = new PDO("mysql:host=localhost", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<p>✅ Connected to MySQL</p>";
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/install.sql');
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "<p>✅ Executed: " . substr($statement, 0, 50) . "...</p>";
            } catch (PDOException $e) {
                // Ignore "database already exists" errors
                if (strpos($e->getMessage(), 'database exists') === false) {
                    echo "<p>⚠️ " . $e->getMessage() . "</p>";
                }
            }
        }
    }
    
    echo "<h2>Setup Complete!</h2>";
    echo "<p>Default admin login:</p>";
    echo "<ul>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Password: <strong>admin123</strong></li>";
    echo "</ul>";
    echo "<p><a href='index.php'>Go to website</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure MySQL is running in XAMPP Control Panel</p>";
}
?>