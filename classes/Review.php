<?php
class Review {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getByGameId($gameId) {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.game_id = :game_id AND r.is_published = 1 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['game_id' => $gameId]);
        return $stmt->fetchAll();
    }
    
    public function getLatest($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT r.*, g.title as game_title, g.slug as game_slug, u.username 
            FROM reviews r 
            JOIN games g ON r.game_id = g.id 
            JOIN users u ON r.user_id = u.id 
            WHERE r.is_published = 1 
            ORDER BY r.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function addReview($gameId, $username, $title, $content, $score, $pros = '', $cons = '') {
        try {
            // Get or create user
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'editor')");
                $stmt->execute([$username, $username . '@temp.com', password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT)]);
                $userId = $this->db->lastInsertId();
            } else {
                $userId = $user['id'];
            }
            
            // Insert review
            $stmt = $this->db->prepare("INSERT INTO reviews (game_id, user_id, title, content, score, pros, cons, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$gameId, $userId, $title, $content, $score, $pros, $cons]);
            
            // Update game average rating
            $this->updateGameRating($gameId);
            
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function updateGameRating($gameId) {
        $stmt = $this->db->prepare("SELECT AVG(score) as avg_rating FROM reviews WHERE game_id = ? AND is_published = 1");
        $stmt->execute([$gameId]);
        $result = $stmt->fetch();
        
        $avgRating = $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
        
        $stmt = $this->db->prepare("UPDATE games SET rating = ? WHERE id = ?");
        $stmt->execute([$avgRating, $gameId]);
    }
}