<?php
class TagModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function findOrCreate($name) {
        $name = trim($name);
        if (empty($name)) return null;

        $stmt = $this->db->prepare("SELECT id FROM tags WHERE name = :name");
        $stmt->execute(['name' => $name]);
        $tag = $stmt->fetch();

        if ($tag) {
            return $tag['id'];
        }

        // Generate a simple slug (not perfect for full Persian, but functional for this basic implementation. Better to use a dedicated slugifier if complex chars exist)
        $slug = mb_strtolower(str_replace(' ', '-', $name));
        $slug = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $slug); // allow letters, numbers, hyphens

        // Ensure slug uniqueness
        $baseSlug = $slug;
        $counter = 1;
        while ($this->findBySlug($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO tags (name, slug) VALUES (:name, :slug)");
            $stmt->execute([
                'name' => $name,
                'slug' => $slug
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // In case of race condition uniqueness failure, try fetching again
            $stmt = $this->db->prepare("SELECT id FROM tags WHERE name = :name");
            $stmt->execute(['name' => $name]);
            $tag = $stmt->fetch();
            return $tag ? $tag['id'] : null;
        }
    }

    public function syncVideoTags($videoId, $tagsArray) {
        $this->db->beginTransaction();
        try {
            // Remove existing tags for this video
            $stmt = $this->db->prepare("DELETE FROM video_tags WHERE video_id = :vid");
            $stmt->execute(['vid' => $videoId]);

            if (!empty($tagsArray)) {
                $tagIds = [];
                foreach ($tagsArray as $tagName) {
                    $id = $this->findOrCreate($tagName);
                    if ($id && !in_array($id, $tagIds)) {
                        $tagIds[] = $id;
                    }
                }

                $stmt = $this->db->prepare("INSERT INTO video_tags (video_id, tag_id) VALUES (:vid, :tid)");
                foreach ($tagIds as $tid) {
                    $stmt->execute(['vid' => $videoId, 'tid' => $tid]);
                }
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getTagsForVideo($videoId) {
        $stmt = $this->db->prepare("SELECT t.* FROM tags t JOIN video_tags vt ON t.id = vt.tag_id WHERE vt.video_id = :vid");
        $stmt->execute(['vid' => $videoId]);
        return $stmt->fetchAll();
    }

    public function getPopularTags($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT t.*, COUNT(vt.video_id) as count
            FROM tags t
            JOIN video_tags vt ON t.id = vt.tag_id
            JOIN videos v ON vt.video_id = v.id
            WHERE v.status = 'published' AND v.visibility = 'public'
            GROUP BY t.id
            ORDER BY count DESC
            LIMIT :limit
        ");
        // Must bind limit as integer
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchTags($query, $limit = 5) {
        $stmt = $this->db->prepare("SELECT name FROM tags WHERE name LIKE :query LIMIT :limit");
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
