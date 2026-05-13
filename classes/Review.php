<?php
class Review {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getByGameId(int $gameId): array {
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
    
    public function getLatest(int $limit = 5): array {
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
}