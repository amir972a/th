<?php
class SocialModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Friendships
    public function getFriendshipStatus($u1, $u2) {
        $stmt = $this->db->prepare("SELECT status, requester_id, addressee_id FROM friendships
                                      WHERE (requester_id = :u1a AND addressee_id = :u2a)
                                         OR (requester_id = :u2b AND addressee_id = :u1b)");
        $stmt->execute([
            'u1a' => $u1, 'u2a' => $u2,
            'u2b' => $u2, 'u1b' => $u1
        ]);
        return $stmt->fetch();
    }

    public function sendFriendRequest($requester, $addressee) {
        $stmt = $this->db->prepare("INSERT INTO friendships (requester_id, addressee_id, status) VALUES (:req, :addr, 'pending')");
        return $stmt->execute(['req' => $requester, 'addr' => $addressee]);
    }

    public function acceptFriendRequest($requester, $addressee) {
        $stmt = $this->db->prepare("UPDATE friendships SET status = 'accepted' WHERE requester_id = :req AND addressee_id = :addr");
        return $stmt->execute(['req' => $requester, 'addr' => $addressee]);
    }

    public function removeFriendship($u1, $u2) {
        $stmt = $this->db->prepare("DELETE FROM friendships WHERE (requester_id = :u1a AND addressee_id = :u2a) OR (requester_id = :u2b AND addressee_id = :u1b)");
        return $stmt->execute([
            'u1a' => $u1, 'u2a' => $u2,
            'u2b' => $u2, 'u1b' => $u1
        ]);
    }

    // Comments
    public function addComment($videoId, $userId, $content) {
        $stmt = $this->db->prepare("INSERT INTO comments (video_id, user_id, content) VALUES (:vid, :uid, :content)");
        return $stmt->execute(['vid' => $videoId, 'uid' => $userId, 'content' => $content]);
    }

    public function getCommentsByVideo($videoId) {
        $stmt = $this->db->prepare("SELECT c.*, u.username, u.display_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.video_id = :vid AND c.status = 'active' ORDER BY c.created_at DESC");
        $stmt->execute(['vid' => $videoId]);
        return $stmt->fetchAll();
    }

    // Ratings
    public function rateVideo($videoId, $userId, $rating) {
        $stmt = $this->db->prepare("INSERT INTO ratings (video_id, user_id, rating) VALUES (:vid, :uid, :rating) ON DUPLICATE KEY UPDATE rating = :rating");
        return $stmt->execute(['vid' => $videoId, 'uid' => $userId, 'rating' => $rating]);
    }

    public function getVideoRating($videoId) {
        $stmt = $this->db->prepare("SELECT SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as likes, SUM(CASE WHEN rating = -1 THEN 1 ELSE 0 END) as dislikes FROM ratings WHERE video_id = :vid");
        $stmt->execute(['vid' => $videoId]);
        return $stmt->fetch();
    }

    public function getUserRating($videoId, $userId) {
        $stmt = $this->db->prepare("SELECT rating FROM ratings WHERE video_id = :vid AND user_id = :uid");
        $stmt->execute(['vid' => $videoId, 'uid' => $userId]);
        $row = $stmt->fetch();
        return $row ? $row['rating'] : null;
    }
}
