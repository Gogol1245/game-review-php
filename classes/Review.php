<?php

// Felhasznaloi recenziok adatbazis-modellje.
class Review
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Egy jatek publikalt recenzioit adja vissza, a szerzo felhasznalonevevel egyutt.
    public function getByGameId($gameId)
    {
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

    // A fooldalon megjeleno legfrissebb recenziokat adja vissza.
    public function getLatest($limit = 5)
    {
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

    // Uj recenziot ment az aktualisan bejelentkezett felhasznalotol.
    // A game.php a user id-t a Sessionbol adja at, nem egy username mezobol.
    public function create($gameId, $userId, $title, $content, $score, $pros = '', $cons = '')
    {
        $stmt = $this->db->prepare("
            INSERT INTO reviews
                (game_id, user_id, title, content, score, pros, cons, is_published)
            VALUES
                (:game_id, :user_id, :title, :content, :score, :pros, :cons, 1)
        ");

        $stmt->execute([
            'game_id' => (int)$gameId,
            'user_id' => (int)$userId,
            'title' => trim($title),
            'content' => trim($content),
            'score' => (int)$score,
            'pros' => trim($pros),
            'cons' => trim($cons),
        ]);

        $this->updateGameRating($gameId);

        return (int)$this->db->lastInsertId();
    }

    // Ujraszamolja a games tablaban tarolt atlagos ertekelest.
    private function updateGameRating($gameId)
    {
        $stmt = $this->db->prepare("
            SELECT AVG(score) as avg_rating
            FROM reviews
            WHERE game_id = ? AND is_published = 1
        ");
        $stmt->execute([$gameId]);
        $result = $stmt->fetch();

        $avgRating = $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;

        $stmt = $this->db->prepare("
            UPDATE games
            SET rating = ?
            WHERE id = ?
        ");
        $stmt->execute([$avgRating, $gameId]);
    }
}
