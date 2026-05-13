<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Adding Review System</h1>";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=game_reviews", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Check if reviews table exists, if not create it
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
    )");
    echo "<p style='color:green'>✅ Reviews table ready</p>";
    
    // Create a regular user for testing (if not exists)
    $password = password_hash('user123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['user', 'user@gamereviews.sk', $password, 'editor']);
    echo "<p style='color:green'>✅ Regular user created (username: user, password: user123)</p>";
    
    echo "<h2 style='color:green'>Setup Complete!</h2>";
    echo "<p><a href='index.php'>Go to website</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>