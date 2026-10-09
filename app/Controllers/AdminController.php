<?php
class AdminController extends Controller {
    public function __construct() {
        // Enforce admin/moderator role
        $role = Session::get('user_role');
        if (!in_array($role, ['admin', 'moderator'])) {
            Session::setFlash('error', 'شما به این بخش دسترسی ندارید.');
            $this->redirect('/');
        }
    }

    public function dashboard() {
        $db = Database::getInstance()->getConnection();

        // Stats
        $stats = [
            'users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'videos' => $db->query("SELECT COUNT(*) FROM videos")->fetchColumn(),
            'pending_videos' => $db->query("SELECT COUNT(*) FROM videos WHERE status = 'processing'")->fetchColumn(),
            'comments' => $db->query("SELECT COUNT(*) FROM comments")->fetchColumn(),
        ];

        $pageTitle = 'پنل مدیریت';
        $contentView = __DIR__ . '/../Views/admin/dashboard.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function users() {
        if (Session::get('user_role') !== 'admin') {
            Session::setFlash('error', 'فقط مدیران کل به این بخش دسترسی دارند.');
            $this->redirect('/admin');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, username, email, display_name, role, status, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();

        $pageTitle = 'مدیریت کاربران';
        $contentView = __DIR__ . '/../Views/admin/users.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function toggleUserStatus($userId) {
        if (Session::get('user_role') !== 'admin' || $this->isPost() === false) {
            $this->redirect('/admin');
        }

        if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
            Session::setFlash('error', 'توکن نامعتبر.');
            $this->redirect('/admin/users');
        }

        $status = $_POST['status'];
        if (in_array($status, ['active', 'suspended', 'banned'])) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE users SET status = :status WHERE id = :id AND id != :my_id");
            $stmt->execute(['status' => $status, 'id' => $userId, 'my_id' => Session::get('user_id')]);
            Session::setFlash('success', 'وضعیت کاربر تغییر یافت.');
        }

        $this->redirect('/admin/users');
    }

    public function videos() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT v.*, u.username FROM videos v JOIN users u ON v.user_id = u.id ORDER BY v.created_at DESC");
        $videos = $stmt->fetchAll();

        $pageTitle = 'مدیریت ویدیوها';
        $contentView = __DIR__ . '/../Views/admin/videos.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function tags() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT t.*, COUNT(vt.video_id) as video_count
            FROM tags t
            LEFT JOIN video_tags vt ON t.id = vt.tag_id
            GROUP BY t.id
            ORDER BY t.name ASC
        ");
        $tags = $stmt->fetchAll();

        $pageTitle = 'مدیریت تگ‌ها';
        $contentView = __DIR__ . '/../Views/admin/tags.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function moderateTag($tagId) {
        if (!$this->isPost()) $this->redirect('/admin/tags');

        if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
            Session::setFlash('error', 'توکن نامعتبر.');
            $this->redirect('/admin/tags');
        }

        $action = $_POST['action'];
        $db = Database::getInstance()->getConnection();

        if ($action === 'rename') {
            $newName = trim($_POST['new_name']);
            if (!empty($newName)) {
                $slug = mb_strtolower(str_replace(' ', '-', $newName));
                $slug = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $slug);
                try {
                    $stmt = $db->prepare("UPDATE tags SET name = :name, slug = :slug WHERE id = :id");
                    $stmt->execute(['name' => $newName, 'slug' => $slug, 'id' => $tagId]);
                    Session::setFlash('success', 'تگ با موفقیت تغییر نام یافت.');
                } catch (PDOException $e) {
                    Session::setFlash('error', 'این نام تگ یا اسلاگ از قبل وجود دارد.');
                }
            }
        } elseif ($action === 'delete') {
            $stmt = $db->prepare("DELETE FROM tags WHERE id = :id");
            $stmt->execute(['id' => $tagId]);
            Session::setFlash('success', 'تگ با موفقیت حذف شد.');
        } elseif ($action === 'merge') {
            $targetId = (int)$_POST['target_id'];
            if ($targetId > 0 && $targetId != $tagId) {
                // Update all video_tags using old tagId to use targetId, ignoring duplicates
                $stmt = $db->prepare("UPDATE IGNORE video_tags SET tag_id = :target WHERE tag_id = :old");
                $stmt->execute(['target' => $targetId, 'old' => $tagId]);
                // Delete old tag (any remaining video_tags for old tagId will cascade delete because they were duplicates)
                $stmt = $db->prepare("DELETE FROM tags WHERE id = :id");
                $stmt->execute(['id' => $tagId]);
                Session::setFlash('success', 'تگ‌ها با موفقیت ادغام شدند.');
            } else {
                Session::setFlash('error', 'تگ مقصد نامعتبر است.');
            }
        }

        $this->redirect('/admin/tags');
    }

    public function moderateVideo($videoId) {
        if (!$this->isPost()) $this->redirect('/admin/videos');

        if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
            Session::setFlash('error', 'توکن نامعتبر.');
            $this->redirect('/admin/videos');
        }

        $action = $_POST['action'];
        $db = Database::getInstance()->getConnection();

        if ($action === 'approve') {
            $stmt = $db->prepare("UPDATE videos SET status = 'published' WHERE id = :id");
            $stmt->execute(['id' => $videoId]);
            Session::setFlash('success', 'ویدیو تایید شد.');
        } elseif ($action === 'reject' || $action === 'remove') {
            $stmt = $db->prepare("UPDATE videos SET status = 'removed' WHERE id = :id");
            $stmt->execute(['id' => $videoId]);
            Session::setFlash('success', 'ویدیو حذف/رد شد.');
        }

        $this->redirect('/admin/videos');
    }
}
