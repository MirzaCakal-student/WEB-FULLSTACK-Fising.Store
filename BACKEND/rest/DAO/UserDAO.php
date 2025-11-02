<?php
require_once "Database.php";

class UserDAO {
    private $conn;
    private $table = "users";

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function create($username, $email, $password_hash, $role = 'user') {
        $sql = "INSERT INTO $this->table (username, email, password_hash, role)
                VALUES (:username, :email, :password_hash, :role)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password_hash", $password_hash);
        $stmt->bindParam(":role", $role);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $username, $email, $role) {
        $sql = "UPDATE $this->table SET username=:username, email=:email, role=:role WHERE user_id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE user_id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
