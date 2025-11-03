<?php
require_once __DIR__ . '/Database.php';

class UserDAO {
    private $conn;
    public function __construct() { $this->conn = Database::connect(); }

    public function getAll() {
        $stmt = $this->conn->query("SELECT user_id, username, email, role, created_at FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT user_id, username, email, role, created_at FROM users WHERE user_id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($username, $email, $password, $role='user') {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("INSERT INTO users(username,email,password_hash,role) VALUES(?,?,?,?)");
        $stmt->execute([$username, $email, $hash, $role]);
        return $this->conn->lastInsertId();
    }

    public function update($id, $username, $email, $password=null) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("UPDATE users SET username=?, email=?, password_hash=? WHERE user_id=?");
            return $stmt->execute([$username,$email,$hash,$id]);
        } else {
            $stmt = $this->conn->prepare("UPDATE users SET username=?, email=? WHERE user_id=?");
            return $stmt->execute([$username,$email,$id]);
        }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id=?");
        return $stmt->execute([$id]);
    }
}
