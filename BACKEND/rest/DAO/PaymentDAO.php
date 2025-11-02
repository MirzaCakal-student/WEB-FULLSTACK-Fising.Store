<?php
require_once "Database.php";

class PaymentDAO {
    private $conn;
    private $table = "payments";

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function create($order_id, $payment_method, $amount, $status) {
        $sql = "INSERT INTO $this->table (order_id, payment_method, amount, status)
                VALUES (:order_id, :payment_method, :amount, :status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":order_id", $order_id);
        $stmt->bindParam(":payment_method", $payment_method);
        $stmt->bindParam(":amount", $amount);
        $stmt->bindParam(":status", $status);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE $this->table SET status=:status WHERE payment_id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE payment_id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
