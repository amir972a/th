<?php
class PageController extends Controller {
    public function show($slug) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM pages WHERE slug = :slug AND status = 'published'");
        $stmt->execute(['slug' => $slug]);
        $page = $stmt->fetch();

        if (!$page) {
            http_response_code(404);
            echo "404 - صفحه یافت نشد";
            return;
        }

        $pageTitle = htmlspecialchars($page['title']);
        $contentView = __DIR__ . '/../Views/page/show.php';
        require_once __DIR__ . '/../Views/layout.php';
    }
}
