<?php
require_once __DIR__ . '/../Models/VideoModel.php';

class HomeController extends Controller {
    public function index() {
        $videoModel = new VideoModel();
        $latestVideos = $videoModel->getLatestPublic(12);

        $pageTitle = 'صفحه اصلی';
        $contentView = __DIR__ . '/../Views/home.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function search() {
        $q = $_GET['q'] ?? '';
        $q = trim($q);

        $videoModel = new VideoModel();

        // Simple search query implementation directly here for speed
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT v.*, u.username, u.display_name
                                    FROM videos v
                                    JOIN users u ON v.user_id = u.id
                                    WHERE v.visibility = 'public' AND v.status = 'published'
                                    AND (v.title LIKE :q OR v.description LIKE :q)
                                    ORDER BY v.created_at DESC LIMIT 20");
        $stmt->execute(['q' => "%$q%"]);
        $videos = $stmt->fetchAll();

        $pageTitle = 'جستجو: ' . $q;
        $contentView = __DIR__ . '/../Views/search.php';
        require_once __DIR__ . '/../Views/layout.php';
    }
}
