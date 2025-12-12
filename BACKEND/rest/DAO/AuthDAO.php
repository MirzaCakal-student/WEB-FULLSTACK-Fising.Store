<?php
require_once __DIR__ . '/BaseDao.php';

class AuthDAO extends BaseDao {

    public function __construct() {
        parent::__construct('users'); // Make sure your table is named 'users'
    }

    public function get_user_by_email($email) {
        // Using string interpolation for table name to be safe
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->connection->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>