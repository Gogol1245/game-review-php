-- Databáza: game_reviews
CREATE DATABASE IF NOT EXISTS game_reviews CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE game_reviews;

-- Tabuľka používateľov
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabuľka hier
CREATE TABLE games (
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
    INDEX idx_slug (slug),
    INDEX idx_genre (genre),
    INDEX idx_platform (platform)
) ENGINE=InnoDB;

-- Tabuľka recenzií
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    score INT NOT NULL CHECK (score >= 1 AND score <= 10),
    pros TEXT,
    cons TEXT,
    is_published TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_game (game_id),
    INDEX idx_user (user_id),
    INDEX idx_score (score)
) ENGINE=InnoDB;

-- Vloženie admin používateľa (heslo: admin123)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@gamereviews.sk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');