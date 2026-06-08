<?php

// A Game osztály a játékok adatbázis-műveleteit fogja össze.
// Így a publikus oldal és az admin felület nem közvetlenül ír SQL-t, hanem ezt az osztályt használja.
class Game
{
    // PDO kapcsolat a lekérdezésekhez.
    private $db;

    // Konstruktor: elkéri a közös adatbázis-kapcsolatot.
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Aktív játékok listázása.
    // Az SQL csak azokat a játékokat kéri le, amelyeket nem töröltünk logikailag.
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

    // Egy játék lekérése URL-barát slug alapján.
    // A publikus game.php ezt használja, például: game.php?slug=elden-ring.
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

    // Egy játék lekérése belső ID alapján.
    // Az admin szerkesztő és törlő oldalak használják.
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

    // Új játék létrehozása.
    // A slug automatikusan a címből készül, hogy szép és könnyen olvasható URL legyen.
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

    // Meglévő játék módosítása.
    // Ha a cím változik, a slug is frissül, de az aktuális rekord ID-ját kizárjuk az ütközésvizsgálatból.
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

    // Játék törlése logikai törléssel.
    // Nem töröljük ki fizikailag a sort, csak inaktívvá tesszük, így később visszakereshető marad.
    public function delete($id)
    {
        $stmt = $this->db->prepare("
            UPDATE games
            SET is_active = 0
            WHERE id = :id
        ");

        return $stmt->execute(['id' => $id]);
    }

    // Az űrlapból érkező adatokat egységesen előkészíti az adatbázishoz.
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

    // URL-barát slug készítése a játék címéből.
    // Példa: "The Witcher 3: Wild Hunt" -> "the-witcher-3-wild-hunt".
    private function createSlug($title, $excludeId = null)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

        $sql = "SELECT COUNT(*) FROM games WHERE slug = :slug";
        $params = ['slug' => $slug];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        // Ez az SQL azt ellenőrzi, létezik-e már ugyanilyen slug.
        // Ha igen, időbélyeget teszünk a végére, hogy egyedi legyen.
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        if ($stmt->fetchColumn() > 0) {
            $slug .= '-' . time();
        }

        return $slug;
    }
}
