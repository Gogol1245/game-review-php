<?php
class Game {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll(int $limit = 10, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT * FROM games 
            WHERE is_active = 1 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT * FROM games WHERE slug = :slug AND is_active = 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
    
    public function create(array $data): int {
        $data['slug'] = $this->createSlug($data['title']);
        
        $stmt = $this->db->prepare("
            INSERT INTO games (title, slug, description, developer, publisher, release_date, genre, platform, image_url)
            VALUES (:title, :slug, :description, :developer, :publisher, :release_date, :genre, :platform, :image_url)
        ");
        
        $stmt->execute([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'developer' => $data['developer'],
            'publisher' => $data['publisher'],
            'release_date' => $data['release_date'],
            'genre' => $data['genre'],
            'platform' => $data['platform'],
            'image_url' => $data['image_url']
        ]);
        
        return (int)$this->db->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $data['slug'] = $this->createSlug($data['title'], $id);
        
        $stmt = $this->db->prepare("
            UPDATE games 
            SET title = :title, slug = :slug, description = :description, 
                developer = :developer, publisher = :publisher, release_date = :release_date,
                genre = :genre, platform = :platform, image_url = :image_url
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'developer' => $data['developer'],
            'publisher' => $data['publisher'],
            'release_date' => $data['release_date'],
            'genre' => $data['genre'],
            'platform' => $data['platform'],
            'image_url' => $data['image_url']
        ]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE games SET is_active = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    private function createSlug(string $title, ?int $excludeId = null): string {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        $sql = "SELECT COUNT(*) FROM games WHERE slug = :slug";
        $params = ['slug' => $slug];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        if ($stmt->fetchColumn() > 0) {
            $slug .= '-' . time();
        }
        
        return $slug;
    }
}