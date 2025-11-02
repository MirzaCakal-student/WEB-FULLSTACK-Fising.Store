<?php
require_once "Database.php";

class OrderDAO {
    private $conn;
    private $table = "orders";

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function create($user_id, $total_amount, $payment_status) {
        $sql = "INSERT INTO $this->table (user_id, total_amount, payment_status)
                VALUES (:user_id, :total_amount, :payment_status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":total_amount", $total_amount);
        $stmt->bindParam(":payment_status", $payment_status);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE $this->table SET payment_status=:status WHERE order_id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE order_id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
