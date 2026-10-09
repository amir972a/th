<?php
class CategoryController extends Controller {
    public function show($slug) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $category = $stmt->fetch();

        if (!$category) {
            http_response_code(404);
            echo "404 - دسته‌بندی یافت نشد";
            return;
        }

        require_once __DIR__ . '/../Models/VideoModel.php';
        // Simplified query for demonstration, should implement pagination and privacy later
        $stmt = $db->prepare("SELECT v.*, u.username, u.display_name FROM videos v JOIN users u ON v.user_id = u.id WHERE v.category_id = :cid AND v.visibility = 'public' AND v.status = 'published' ORDER BY v.created_at DESC LIMIT 20");
        $stmt->execute(['cid' => $category['id']]);
        $videos = $stmt->fetchAll();

        $pageTitle = 'دسته‌بندی: ' . htmlspecialchars($category['name']);
        $contentView = __DIR__ . '/../Views/category/show.php';
        require_once __DIR__ . '/../Views/layout.php';
    }
}
