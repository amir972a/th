<?php
class SocialController extends Controller {
    private $socialModel;

    public function __construct() {
        $this->socialModel = new SocialModel();
    }

    public function addComment($videoId) {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        if ($this->isPost()) {
            if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
                Session::setFlash('error', 'توکن امنیتی نامعتبر است.');
                $this->redirect('/watch/' . $videoId);
            }

            $content = trim($_POST['content']);
            if (!empty($content)) {
                $this->socialModel->addComment($videoId, $userId, $content);
                Session::setFlash('success', 'نظر شما ثبت شد.');
            }
        }
        $this->redirect('/watch/' . $videoId);
    }

    public function rateVideo($videoId) {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        if ($this->isPost()) {
            if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
                Session::setFlash('error', 'توکن نامعتبر.');
                $this->redirect('/watch/' . $videoId);
            }

            $rating = isset($_POST['rating']) && $_POST['rating'] === 'dislike' ? -1 : 1;
            $this->socialModel->rateVideo($videoId, $userId, $rating);
        }
        $this->redirect('/watch/' . $videoId);
    }

    public function friendAction($targetId) {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        if ($this->isPost() && Security::verifyCSRFToken($_POST['csrf_token'])) {
            $action = $_POST['action'];
            if ($action === 'request') {
                $this->socialModel->sendFriendRequest($userId, $targetId);
                Session::setFlash('success', 'درخواست دوستی ارسال شد.');
            } elseif ($action === 'accept') {
                $this->socialModel->acceptFriendRequest($targetId, $userId);
                Session::setFlash('success', 'درخواست دوستی تایید شد.');
            } elseif ($action === 'remove' || $action === 'cancel' || $action === 'reject') {
                $this->socialModel->removeFriendship($userId, $targetId);
                Session::setFlash('success', 'عملیات با موفقیت انجام شد.');
            }
        }
        // Redirect back where they came from
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit;
    }
}
