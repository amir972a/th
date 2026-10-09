<?php
class VideoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $title, $description, $categoryId, $filename, $visibility, $status = 'processing') {
        $stmt = $this->db->prepare("INSERT INTO videos (user_id, title, description, category_id, filename, visibility, status) VALUES (:user_id, :title, :description, :category_id, :filename, :visibility, :status)");
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'category_id' => $categoryId,
            'filename' => $filename,
            'visibility' => $visibility,
            'status' => $status
        ]);
        return $this->db->lastInsertId();
    }

    public function updateThumbnail($videoId, $thumbnailPath) {
        $stmt = $this->db->prepare("UPDATE videos SET thumbnail_path = :thumbnail, status = 'published' WHERE id = :id");
        return $stmt->execute([
            'thumbnail' => $thumbnailPath,
            'id' => $videoId
        ]);
    }

    public function update($videoId, $title, $description, $categoryId, $visibility) {
        $stmt = $this->db->prepare("UPDATE videos SET title = :title, description = :description, category_id = :category_id, visibility = :visibility WHERE id = :id");
        return $stmt->execute([
            'title' => $title,
            'description' => $description,
            'category_id' => $categoryId,
            'visibility' => $visibility,
            'id' => $videoId
        ]);
    }

    public function updateStatus($videoId, $status) {
        $stmt = $this->db->prepare("UPDATE videos SET status = :status WHERE id = :id");
        return $stmt->execute([
            'status' => $status,
            'id' => $videoId
        ]);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT v.*, u.username, u.display_name, c.name as category_name
                                    FROM videos v
                                    JOIN users u ON v.user_id = u.id
                                    LEFT JOIN categories c ON v.category_id = c.id
                                    WHERE v.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getLatestPublic($limit = 12) {
        $stmt = $this->db->prepare("SELECT v.*, u.username, u.display_name
                                    FROM videos v
                                    JOIN users u ON v.user_id = u.id
                                    WHERE v.visibility = 'public' AND v.status = 'published'
                                    ORDER BY v.created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function incrementViews($id) {
        $stmt = $this->db->prepare("UPDATE videos SET views = views + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getCategories() {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
