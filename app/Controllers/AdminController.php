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

    public function categories() {
        if (Session::get('user_role') !== 'admin') {
            $this->redirect('/admin');
        }
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT c.*, COUNT(v.id) as video_count
            FROM categories c
            LEFT JOIN videos v ON c.id = v.category_id
            GROUP BY c.id ORDER BY c.name ASC
        ");
        $categories = $stmt->fetchAll();
        $pageTitle = 'مدیریت دسته‌بندی‌ها';
        $contentView = __DIR__ . '/../Views/admin/categories.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function addCategory() {
        if (Session::get('user_role') !== 'admin' || !$this->isPost()) $this->redirect('/admin/categories');
        if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
            Session::setFlash('error', 'توکن نامعتبر.');
            $this->redirect('/admin/categories');
        }

        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        if ($name && $slug) {
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO categories (name, slug) VALUES (:name, :slug)");
                $stmt->execute(['name' => $name, 'slug' => $slug]);
                Session::setFlash('success', 'دسته‌بندی افزوده شد.');
            } catch (Exception $e) {
                Session::setFlash('error', 'خطا در افزودن (شاید اسلاگ تکراری باشد).');
            }
        }
        $this->redirect('/admin/categories');
    }

    public function pages() {
        if (Session::get('user_role') !== 'admin') {
            $this->redirect('/admin');
        }
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM pages ORDER BY created_at DESC");
        $pages = $stmt->fetchAll();
        $pageTitle = 'مدیریت صفحات';
        $contentView = __DIR__ . '/../Views/admin/pages.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function addPage() {
        if (Session::get('user_role') !== 'admin' || !$this->isPost()) $this->redirect('/admin/pages');
        if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
            Session::setFlash('error', 'توکن نامعتبر.');
            $this->redirect('/admin/pages');
        }

        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']);
        $content = trim($_POST['content']);
        if ($title && $slug && $content) {
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("INSERT INTO pages (title, slug, content) VALUES (:title, :slug, :content)");
                $stmt->execute(['title' => $title, 'slug' => $slug, 'content' => $content]);
                Session::setFlash('success', 'صفحه افزوده شد.');
            } catch (Exception $e) {
                Session::setFlash('error', 'خطا در افزودن (شاید اسلاگ تکراری باشد).');
            }
        } else {
            Session::setFlash('error', 'پر کردن همه فیلدها الزامی است.');
        }
        $this->redirect('/admin/pages');
    }

    public function headerSettings() {
        if (Session::get('user_role') !== 'admin') {
            Session::setFlash('error', 'فقط مدیران کل به این بخش دسترسی دارند.');
            $this->redirect('/admin');
        }

        require_once __DIR__ . '/../Models/SettingModel.php';
        $settingModel = new SettingModel();
        require_once __DIR__ . '/../Core/ViewHelper.php';

        if ($this->isPost()) {
            if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
                Session::setFlash('error', 'توکن نامعتبر.');
                $this->redirect('/admin/settings/header');
            }

            $config = [
                'welcome_msg' => $_POST['welcome_msg'] ?? '',
                'logo_text_1' => $_POST['logo_text_1'] ?? '',
                'logo_text_2' => $_POST['logo_text_2'] ?? '',
                'logo_url' => $_POST['logo_url'] ?? '',
                'search_enabled' => isset($_POST['search_enabled']),
                'auth_enabled' => isset($_POST['auth_enabled']),
                'upload_enabled' => isset($_POST['upload_enabled']),
                'menu' => []
            ];

            if (isset($_POST['menu']) && is_array($_POST['menu'])) {
                foreach ($_POST['menu'] as $item) {
                    if (!empty($item['label'])) {
                        $config['menu'][] = [
                            'label' => $item['label'],
                            'url' => $item['url'] ?? '#',
                            'enabled' => isset($item['enabled'])
                        ];
                    }
                }
            }

            $settingModel->set('header_config', json_encode($config, JSON_UNESCAPED_UNICODE));
            Session::setFlash('success', 'تنظیمات هدر با موفقیت ذخیره شد.');
            $this->redirect('/admin/settings/header');
        }

        $headerConfig = ViewHelper::getHeaderConfig();

        $db = Database::getInstance()->getConnection();
        $categories = $db->query("SELECT id, name, slug FROM categories ORDER BY name ASC")->fetchAll();
        $pages = $db->query("SELECT id, title, slug FROM pages WHERE status = 'published' ORDER BY title ASC")->fetchAll();

        $pageTitle = 'تنظیمات هدر';
        $contentView = __DIR__ . '/../Views/admin/settings_header.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function footerSettings() {
        if (Session::get('user_role') !== 'admin') {
            Session::setFlash('error', 'فقط مدیران کل به این بخش دسترسی دارند.');
            $this->redirect('/admin');
        }

        require_once __DIR__ . '/../Models/SettingModel.php';
        $settingModel = new SettingModel();
        require_once __DIR__ . '/../Core/ViewHelper.php';

        if ($this->isPost()) {
            if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
                Session::setFlash('error', 'توکن نامعتبر.');
                $this->redirect('/admin/settings/footer');
            }

            $config = [
                'copyright_text' => $_POST['copyright_text'] ?? '',
                'description' => $_POST['description'] ?? '',
                'logo_url' => $_POST['logo_url'] ?? '',
                'columns' => [],
                'social_links' => []
            ];

            if (isset($_POST['col']) && is_array($_POST['col'])) {
                foreach ($_POST['col'] as $columnItem) {
                    if (!empty($columnItem['title'])) {
                        $col = [
                            'title' => $columnItem['title'],
                            'enabled' => isset($columnItem['enabled']),
                            'links' => []
                        ];

                        if (isset($columnItem['links']) && is_array($columnItem['links'])) {
                            foreach ($columnItem['links'] as $linkItem) {
                                if (!empty($linkItem['label'])) {
                                    $col['links'][] = [
                                        'label' => $linkItem['label'],
                                        'url' => $linkItem['url'] ?? '#',
                                        'enabled' => isset($linkItem['enabled'])
                                    ];
                                }
                            }
                        }
                        $config['columns'][] = $col;
                    }
                }
            }

            if (isset($_POST['social']) && is_array($_POST['social'])) {
                foreach ($_POST['social'] as $socialItem) {
                    if (!empty($socialItem['icon'])) {
                        $config['social_links'][] = [
                            'icon' => $socialItem['icon'],
                            'url' => $socialItem['url'] ?? '#',
                            'enabled' => isset($socialItem['enabled'])
                        ];
                    }
                }
            }

            $settingModel->set('footer_config', json_encode($config, JSON_UNESCAPED_UNICODE));
            Session::setFlash('success', 'تنظیمات فوتر با موفقیت ذخیره شد.');
            $this->redirect('/admin/settings/footer');
        }

        $footerConfig = ViewHelper::getFooterConfig();

        $db = Database::getInstance()->getConnection();
        $categories = $db->query("SELECT id, name, slug FROM categories ORDER BY name ASC")->fetchAll();
        $pages = $db->query("SELECT id, title, slug FROM pages WHERE status = 'published' ORDER BY title ASC")->fetchAll();

        $pageTitle = 'تنظیمات فوتر';
        $contentView = __DIR__ . '/../Views/admin/settings_footer.php';
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
