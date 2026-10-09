<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($username, $email, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, display_name) VALUES (:username, :email, :password_hash, :display_name)");
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $hash,
            'display_name' => $username // default display name to username
        ]);
    }

    public function updateProfile($id, $displayName, $bio, $gender = null, $interests = null) {
        $stmt = $this->db->prepare("UPDATE users SET display_name = :display_name, bio = :bio, gender = :gender, interests = :interests WHERE id = :id");
        return $stmt->execute([
            'display_name' => $displayName,
            'bio' => $bio,
            'gender' => $gender,
            'interests' => $interests,
            'id' => $id
        ]);
    }
}
