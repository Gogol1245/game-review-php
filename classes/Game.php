<?php

// Jatekok adatbazis-modellje. Az admin oldalak CRUD muveletekre,
// a publikus oldalak listazasra es reszletezo oldalra hasznaljak.
class Game
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Csak aktiv jatekokat ad vissza. A logikailag torolt jatekok is_active erteke 0.
    public function getAll($limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM games
            WHERE is_active = 1
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // A publikus jatekoldal a game.php?slug=... parameterbol dolgozik.
    public function getBySlug($slug)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM games
            WHERE slug = :slug AND is_active = 1
        ");
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }

    // Az admin szerkesztes es torles numerikus id-t hasznal.
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM games
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    // Jatekot hoz letre az admin urlapbol, es egyedi slugot general.
    public function create($data)
    {
        $data = $this->prepareGameData($data);
        $data['slug'] = $this->createSlug($data['title']);

        $stmt = $this->db->prepare("
            INSERT INTO games
                (title, slug, description, developer, publisher, release_date, genre, platform, image_url, rating)
            VALUES
                (:title, :slug, :description, :developer, :publisher, :release_date, :genre, :platform, :image_url, :rating)
        ");
        $stmt->execute($data);

        return (int)$this->db->lastInsertId();
    }

    // Frissiti a jatek adatait, es cimvaltozasnal a slugot is ujrageneralja.
    public function update($id, $data)
    {
        $data = $this->prepareGameData($data);
        $data['id'] = (int)$id;
        $data['slug'] = $this->createSlug($data['title'], $id);

        $stmt = $this->db->prepare("
            UPDATE games
            SET
                title = :title,
                slug = :slug,
                description = :description,
                developer = :developer,
                publisher = :publisher,
                release_date = :release_date,
                genre = :genre,
                platform = :platform,
                image_url = :image_url,
                rating = :rating
            WHERE id = :id
        ");

        return $stmt->execute($data);
    }

    // Logikai torles: a sor megmarad az adatbazisban, de eltunik a publikus listakbol.
    public function delete($id)
    {
        $stmt = $this->db->prepare("
            UPDATE games
            SET is_active = 0
            WHERE id = :id
        ");

        return $stmt->execute(['id' => $id]);
    }

    private function prepareGameData($data)
    {
        return [
            'title' => trim($data['title'] ?? ''),
            'description' => trim($data['description'] ?? ''),
            'developer' => trim($data['developer'] ?? ''),
            'publisher' => trim($data['publisher'] ?? ''),
            'release_date' => ($data['release_date'] ?? '') ?: null,
            'genre' => trim($data['genre'] ?? ''),
            'platform' => trim($data['platform'] ?? ''),
            'image_url' => trim($data['image_url'] ?? ''),
            'rating' => isset($data['rating']) ? (float)$data['rating'] : 0,
        ];
    }

    // A cimből URL-barat slugot keszit, utkozes eseten idobelyeget fuz hozza.
    private function createSlug($title, $excludeId = null)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

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
