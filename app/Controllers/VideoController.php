<?php
class VideoController extends Controller {
    private $videoModel;

    public function __construct() {
        $this->videoModel = new VideoModel();
    }

    public function upload() {
        if (!Session::get('user_id')) {
            $this->redirect('/login');
        }

        if ($this->isPost()) {
            if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
                Session::setFlash('error', 'توکن امنیتی نامعتبر است.');
                $this->redirect('/upload');
            }

            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $categoryId = $_POST['category_id'] ?: null;
            $visibility = $_POST['visibility'] === 'private' ? 'private' : 'public';

            // Process tags
            $tagsInput = $_POST['tags'] ?? '';
            $tagsArray = [];
            if (!empty(trim($tagsInput))) {
                $rawTags = explode(',', $tagsInput);
                foreach ($rawTags as $tag) {
                    $cleanTag = trim($tag);
                    if (!empty($cleanTag) && !in_array($cleanTag, $tagsArray)) {
                        $tagsArray[] = $cleanTag;
                    }
                }
            }

            $errors = [];

            if (empty($title)) {
                $errors[] = 'عنوان ویدیو الزامی است.';
            }

            if (!isset($_FILES['video_file']) || $_FILES['video_file']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'خطا در آپلود فایل ویدیو.';
            } else {
                $file = $_FILES['video_file'];
                $allowedMimes = ['video/mp4', 'video/webm', 'video/ogg'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mime, $allowedMimes)) {
                    $errors[] = 'فرمت فایل مجاز نیست. فقط MP4، WebM و OGG پذیرفته می‌شود.';
                }

                // Prevent executable uploads
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $disallowedExts = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'sh', 'bat'];
                if (in_array($ext, $disallowedExts)) {
                    $errors[] = 'این نوع فایل مجاز نیست.';
                }
            }

            if (empty($errors)) {
                $filename = uniqid('vid_') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetDir = $visibility === 'public' ? STORAGE_PATH . '/videos/public/' : STORAGE_PATH . '/videos/private/';

                // Ensure directory exists
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                $targetFile = $targetDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    $videoId = $this->videoModel->create(Session::get('user_id'), $title, $description, $categoryId, $filename, $visibility, 'processing');

                    // Simple FFmpeg thumbnail generation (fire and forget for now, in reality use a queue)
                    $thumbnailName = uniqid('thumb_') . '.jpg';
                    $thumbPath = STORAGE_PATH . '/thumbnails/' . $thumbnailName;

                    if (!is_dir(STORAGE_PATH . '/thumbnails/')) {
                        mkdir(STORAGE_PATH . '/thumbnails/', 0755, true);
                    }

                    // Background process to generate thumbnail
                    $cmd = "ffmpeg -i " . escapeshellarg($targetFile) . " -ss 00:00:01.000 -vframes 1 " . escapeshellarg($thumbPath) . " > /dev/null 2>&1";
                    exec($cmd, $output, $returnVar);

                    if ($returnVar === 0 && file_exists($thumbPath)) {
                        $this->videoModel->updateThumbnail($videoId, $thumbnailName);
                    } else {
                        // Mark as published anyway without thumbnail
                        $this->videoModel->updateStatus($videoId, 'published');
                    }

                    // Sync tags
                    if (!empty($tagsArray)) {
                        require_once __DIR__ . '/../Models/TagModel.php';
                        $tagModel = new TagModel();
                        $tagModel->syncVideoTags($videoId, $tagsArray);
                    }

                    Session::setFlash('success', 'ویدیو با موفقیت آپلود شد.');
                    $this->redirect('/watch/' . $videoId);
                } else {
                    $errors[] = 'خطا در ذخیره فایل در سرور.';
                }
            }

            if (!empty($errors)) {
                foreach ($errors as $err) {
                    Session::setFlash('error', $err);
                }
            }
        }

        $categories = $this->videoModel->getCategories();
        $pageTitle = 'آپلود ویدیو';
        $contentView = __DIR__ . '/../Views/video/upload.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function edit($id) {
        if (!Session::get('user_id')) {
            $this->redirect('/login');
        }

        $video = $this->videoModel->findById($id);
        if (!$video) {
            Session::setFlash('error', 'ویدیو یافت نشد.');
            $this->redirect('/');
        }

        $isOwner = $video['user_id'] == Session::get('user_id');
        $isAdmin = in_array(Session::get('user_role'), ['admin', 'moderator']);

        if (!$isOwner && !$isAdmin) {
            Session::setFlash('error', 'شما دسترسی لازم برای ویرایش این ویدیو را ندارید.');
            $this->redirect('/watch/' . $id);
        }

        require_once __DIR__ . '/../Models/TagModel.php';
        $tagModel = new TagModel();

        if ($this->isPost()) {
            if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
                Session::setFlash('error', 'توکن امنیتی نامعتبر است.');
                $this->redirect('/video/' . $id . '/edit');
            }

            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $categoryId = $_POST['category_id'] ?: null;
            $visibility = $_POST['visibility'] === 'private' ? 'private' : 'public';

            $tagsInput = $_POST['tags'] ?? '';
            $tagsArray = [];
            if (!empty(trim($tagsInput))) {
                $rawTags = explode(',', $tagsInput);
                foreach ($rawTags as $tag) {
                    $cleanTag = trim($tag);
                    if (!empty($cleanTag) && !in_array($cleanTag, $tagsArray)) {
                        $tagsArray[] = $cleanTag;
                    }
                }
            }

            if (empty($title)) {
                Session::setFlash('error', 'عنوان ویدیو الزامی است.');
            } else {
                $this->videoModel->update($id, $title, $description, $categoryId, $visibility);
                $tagModel->syncVideoTags($id, $tagsArray);
                Session::setFlash('success', 'ویدیو با موفقیت ویرایش شد.');
                $this->redirect('/watch/' . $id);
            }
        }

        $tags = $tagModel->getTagsForVideo($id);
        $categories = $this->videoModel->getCategories();
        $pageTitle = 'ویرایش ویدیو';
        $contentView = __DIR__ . '/../Views/video/edit.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function watch($id) {
        $video = $this->videoModel->findById($id);

        if (!$video || $video['status'] !== 'published') {
            Session::setFlash('error', 'ویدیو یافت نشد یا در دسترس نیست.');
            $this->redirect('/');
        }

        // Authorization check for private videos
        if ($video['visibility'] === 'private') {
            $userId = Session::get('user_id');
            if (!$userId) {
                Session::setFlash('error', 'برای مشاهده این ویدیو باید وارد شوید.');
                $this->redirect('/login');
            }

            $isOwner = $video['user_id'] == $userId;
            $isAdmin = in_array(Session::get('user_role'), ['admin', 'moderator']);

            // Check friendship if not owner or admin
            $isFriend = false;
            if (!$isOwner && !$isAdmin) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT status FROM friendships
                                      WHERE ((requester_id = :u1a AND addressee_id = :u2a)
                                         OR (requester_id = :u2b AND addressee_id = :u1b))
                                        AND status = 'accepted'");
                $stmt->execute([
                    'u1a' => $userId, 'u2a' => $video['user_id'],
                    'u2b' => $video['user_id'], 'u1b' => $userId
                ]);
                if ($stmt->fetch()) {
                    $isFriend = true;
                }
            }

            if (!$isOwner && !$isAdmin && !$isFriend) {
                Session::setFlash('error', 'شما دسترسی لازم برای مشاهده این ویدیوی خصوصی را ندارید.');
                $this->redirect('/');
            }
        }

        // Increment views (simple session based anti-spam could be added)
        if (!isset($_SESSION['viewed_' . $id])) {
            $this->videoModel->incrementViews($id);
            $_SESSION['viewed_' . $id] = true;
            $video['views']++;
        }

        // Fetch social data
        require_once __DIR__ . '/../Models/SocialModel.php';
        $socialModel = new SocialModel();
        $comments = $socialModel->getCommentsByVideo($id);
        $ratings = $socialModel->getVideoRating($id);
        $userRating = Session::get('user_id') ? $socialModel->getUserRating($id, Session::get('user_id')) : null;

        // Fetch tags
        require_once __DIR__ . '/../Models/TagModel.php';
        $tagModel = new TagModel();
        $tags = $tagModel->getTagsForVideo($id);

        $pageTitle = $video['title'];
        $contentView = __DIR__ . '/../Views/video/watch.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function stream($id) {
        $video = $this->videoModel->findById($id);
        if (!$video) {
            http_response_code(404);
            exit;
        }

        // Auth check logic same as watch()
        if ($video['visibility'] === 'private') {
            $userId = Session::get('user_id');
            if (!$userId) {
                http_response_code(403);
                exit;
            }
            $isOwner = $video['user_id'] == $userId;
            $isAdmin = in_array(Session::get('user_role'), ['admin', 'moderator']);
            $isFriend = false;
            if (!$isOwner && !$isAdmin) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT status FROM friendships
                                      WHERE ((requester_id = :u1a AND addressee_id = :u2a)
                                         OR (requester_id = :u2b AND addressee_id = :u1b))
                                        AND status = 'accepted'");
                $stmt->execute([
                    'u1a' => $userId, 'u2a' => $video['user_id'],
                    'u2b' => $video['user_id'], 'u1b' => $userId
                ]);
                if ($stmt->fetch()) {
                    $isFriend = true;
                }
            }
            if (!$isOwner && !$isAdmin && !$isFriend) {
                http_response_code(403);
                exit;
            }
        }

        $path = STORAGE_PATH . '/videos/' . $video['visibility'] . '/' . $video['filename'];
        if (!file_exists($path)) {
            http_response_code(404);
            exit;
        }

        $this->serveFileWithRange($path);
    }

    public function thumbnail($filename) {
        // Simple secure serving of thumbnails, avoid path traversal
        $filename = basename($filename);
        $path = STORAGE_PATH . '/thumbnails/' . $filename;

        if (file_exists($path)) {
            header('Content-Type: image/jpeg');
            readfile($path);
        } else {
            // Serve default thumb
            header('Content-Type: image/png');
            // Assuming we have a default thumb or just output a blank square
            echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
        }
    }

    private function serveFileWithRange($filePath) {
        $fp = @fopen($filePath, 'rb');
        $size = filesize($filePath);
        $length = $size;
        $start = 0;
        $end = $size - 1;

        header('Content-type: video/mp4'); // simple assumption, should ideally be dynamic
        header('Accept-Ranges: bytes');

        if (isset($_SERVER['HTTP_RANGE'])) {
            $c_start = $start;
            $c_end = $end;
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            if ($range == '-') {
                $c_start = $size - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $c_start = $range[0];
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size - 1;
            }
            $c_end = ($c_end > $end) ? $end : $c_end;
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            $start = $c_start;
            $end = $c_end;
            $length = $end - $start + 1;
            fseek($fp, $start);
            header('HTTP/1.1 206 Partial Content');
        }

        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: $length");

        $buffer = 1024 * 8;
        while (!feof($fp) && ($p = ftell($fp)) <= $end) {
            if ($p + $buffer > $end) {
                $buffer = $end - $p + 1;
            }
            set_time_limit(0);
            echo fread($fp, $buffer);
            flush();
        }
        fclose($fp);
        exit;
    }
}
