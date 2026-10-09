<?php
class ProfileController extends Controller {
    public function show($username = null) {
        if (!$username) {
            // View own profile
            $username = Session::get('username');
            if (!$username) {
                $this->redirect('/login');
            }
            $this->redirect('/profile/' . $username);
        }

        require_once __DIR__ . '/../Models/UserModel.php';
        require_once __DIR__ . '/../Models/SocialModel.php';
        require_once __DIR__ . '/../Models/VideoModel.php';

        $userModel = new UserModel();
        $socialModel = new SocialModel();

        $profileUser = $userModel->findByUsername($username);
        if (!$profileUser) {
            Session::setFlash('error', 'کاربر یافت نشد.');
            $this->redirect('/');
        }

        $isOwner = Session::get('user_id') == $profileUser['id'];
        $friendStatus = null;
        $isFriend = false;

        if (Session::get('user_id') && !$isOwner) {
            $friendStatus = $socialModel->getFriendshipStatus(Session::get('user_id'), $profileUser['id']);
            if ($friendStatus && $friendStatus['status'] === 'accepted') {
                $isFriend = true;
            }
        }

        $db = Database::getInstance()->getConnection();

        // Fetch videos
        if ($isOwner) {
            $stmt = $db->prepare("SELECT v.*, u.username, u.display_name FROM videos v JOIN users u ON v.user_id = u.id WHERE v.user_id = :uid AND v.status != 'removed' ORDER BY v.created_at DESC");
        } elseif ($isFriend || Session::get('user_role') === 'admin') {
            $stmt = $db->prepare("SELECT v.*, u.username, u.display_name FROM videos v JOIN users u ON v.user_id = u.id WHERE v.user_id = :uid AND v.status = 'published' ORDER BY v.created_at DESC");
        } else {
            $stmt = $db->prepare("SELECT v.*, u.username, u.display_name FROM videos v JOIN users u ON v.user_id = u.id WHERE v.user_id = :uid AND v.visibility = 'public' AND v.status = 'published' ORDER BY v.created_at DESC");
        }
        $stmt->execute(['uid' => $profileUser['id']]);
        $videos = $stmt->fetchAll();

        // If owner, fetch incoming friend requests
        $pendingRequests = [];
        if ($isOwner) {
            $stmt = $db->prepare("SELECT f.*, u.username, u.display_name FROM friendships f JOIN users u ON f.requester_id = u.id WHERE f.addressee_id = :uid AND f.status = 'pending'");
            $stmt->execute(['uid' => $profileUser['id']]);
            $pendingRequests = $stmt->fetchAll();
        }

        // Fetch friends
        $stmt = $db->prepare("
            SELECT u.id, u.username, u.display_name
            FROM friendships f
            JOIN users u ON (f.requester_id = u.id OR f.addressee_id = u.id)
            WHERE (f.requester_id = :uid1 OR f.addressee_id = :uid2) AND f.status = 'accepted' AND u.id != :uid3
        ");
        $stmt->execute([
            'uid1' => $profileUser['id'],
            'uid2' => $profileUser['id'],
            'uid3' => $profileUser['id']
        ]);
        $friends = $stmt->fetchAll();

        $pageTitle = 'پروفایل ' . $profileUser['display_name'];
        $contentView = __DIR__ . '/../Views/profile/view.php';
        require_once __DIR__ . '/../Views/layout.php';
    }
}
