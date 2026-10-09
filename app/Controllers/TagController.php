<?php
class TagController extends Controller {
    public function show($slug) {
        require_once __DIR__ . '/../Models/TagModel.php';
        require_once __DIR__ . '/../Models/VideoModel.php';
        require_once __DIR__ . '/../Models/SocialModel.php';

        $tagModel = new TagModel();
        $tag = $tagModel->findBySlug($slug);

        if (!$tag) {
            http_response_code(404);
            $pageTitle = 'تگ یافت نشد';
            // Simple 404 response
            echo "404 - تگ یافت نشد";
            return;
        }

        $sort = $_GET['sort'] ?? 'newest';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $db = Database::getInstance()->getConnection();

        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');

        $baseQuery = "FROM videos v
                      JOIN video_tags vt ON v.id = vt.video_id
                      JOIN users u ON v.user_id = u.id
                      WHERE vt.tag_id = :tid AND v.status = 'published'";

        $privacyCondition = " AND (v.visibility = 'public'";
        $params = ['tid' => $tag['id']];

        if ($userId) {
            if ($userRole === 'admin') {
                $privacyCondition = "";
            } else {
                $privacyCondition .= " OR v.user_id = :uid";
                $params['uid'] = $userId;

                $privacyCondition .= " OR EXISTS (
                    SELECT 1 FROM friendships f
                    WHERE ((f.requester_id = :uid_f1 AND f.addressee_id = v.user_id)
                       OR (f.requester_id = v.user_id AND f.addressee_id = :uid_f2))
                    AND f.status = 'accepted'
                )";
                $params['uid_f1'] = $userId;
                $params['uid_f2'] = $userId;

                $privacyCondition .= ")";
            }
        } else {
            $privacyCondition .= ")";
        }

        $countQuery = "SELECT COUNT(v.id) " . $baseQuery . $privacyCondition;
        $stmtCount = $db->prepare($countQuery);
        $stmtCount->execute($params);
        $totalVideos = $stmtCount->fetchColumn();
        $totalPages = ceil($totalVideos / $limit);

        $orderClause = " ORDER BY v.created_at DESC";
        if ($sort === 'popular') {
            $orderClause = " ORDER BY v.views DESC, v.created_at DESC";
        }

        $selectQuery = "SELECT v.*, u.username, u.display_name " . $baseQuery . $privacyCondition . $orderClause . " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $db->prepare($selectQuery);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        $videos = $stmt->fetchAll();

        $popularTags = $tagModel->getPopularTags(10);

        $pageTitle = 'تگ: ' . htmlspecialchars($tag['name']);
        $contentView = __DIR__ . '/../Views/tag/show.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function search() {
        $query = $_GET['q'] ?? '';
        header('Content-Type: application/json');

        if (empty($query)) {
            echo json_encode([]);
            return;
        }

        require_once __DIR__ . '/../Models/TagModel.php';
        $tagModel = new TagModel();
        $tags = $tagModel->searchTags($query);

        echo json_encode($tags);
    }
}
