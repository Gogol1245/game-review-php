<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Password Fix</h1>";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=game_reviews", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Create new password hash
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    echo "<p>New password hash: " . $password . "</p>";
    
    // Delete old admin user
    $pdo->exec("DELETE FROM users WHERE username = 'admin'");
    echo "<p style='color:green'>✅ Old admin user deleted</p>";
    
    // Insert new admin user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@gamereviews.sk', $password, 'admin']);
    echo "<p style='color:green'>✅ New admin user created with fresh password hash</p>";
    
    // Verify the user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p style='color:green'>✅ Admin user verified in database</p>";
        
        // Test password verification
        if (password_verify('admin123', $user['password'])) {
            echo "<p style='color:green'>✅ Password verification test PASSED</p>";
        } else {
            echo "<p style='color:red'>❌ Password verification test FAILED</p>";
        }
    }
    
    echo "<h2 style='color:green; margin-top:30px;'>FIX COMPLETE!</h2>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='admin/login.php'>GO TO LOGIN PAGE</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>