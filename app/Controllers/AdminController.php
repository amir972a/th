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
