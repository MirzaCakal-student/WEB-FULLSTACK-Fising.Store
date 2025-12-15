<?php
 //finished
require_once __DIR__ . '/BaseDao.php';

class UserDAO extends BaseDao {

    public function __construct() {
        // users(user_id, username, email, password_hash, role, created_at)
        parent::__construct('users', 'user_id');
    }

    public function getByEmail($email) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM users WHERE email = :email"
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}