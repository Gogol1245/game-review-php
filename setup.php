<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🎮 Game Reviews - Complete Setup</h1>";

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3307", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<p style='color:green'>✅ Connected to MySQL</p>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS game_reviews CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color:green'>✅ Database created</p>";
    
    $pdo->exec("USE game_reviews");
    
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'editor') DEFAULT 'editor',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color:green'>✅ Users table created</p>";
    
    // Games table
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
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "<p style='color:green'>✅ Games table created</p>";
    
    // Reviews table
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
    echo "<p style='color:green'>✅ Reviews table created</p>";
    
    // Create admin user
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@gamereviews.sk', $password, 'admin']);
    echo "<p style='color:green'>✅ Admin user (admin/admin123)</p>";
    
    // Create regular user
    $stmt->execute(['user', 'user@gamereviews.sk', $password, 'editor']);
    echo "<p style='color:green'>✅ Test user (user/user123)</p>";
    
    echo "<h2 style='color:green; margin-top:30px;'>✅ SETUP COMPLETE!</h2>";
    echo "<div style='background:#e8f5e9; padding:20px; border-radius:8px; margin:20px 0;'>";
    echo "<h3>Login Information:</h3>";
    echo "<p><strong>Admin:</strong> admin / admin123</p>";
    echo "<p><strong>User:</strong> user / user123</p>";
    echo "</div>";
    echo "<p><a href='index.php' style='font-size:18px; padding:10px 20px; background:#e94560; color:white; text-decoration:none; border-radius:5px;'>🎮 GO TO WEBSITE</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
