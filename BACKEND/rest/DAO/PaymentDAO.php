<?php
require_once __DIR__ . '/Database.php';

class PaymentDAO {
    private $conn;
    public function __construct() { $this->conn = Database::connect(); }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM payments ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByOrder($order_id) {
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE order_id=?");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($order_id, $method, $amount, $status='initiated') {
        $stmt = $this->conn->prepare("INSERT INTO payments(order_id,payment_method,amount,status) VALUES(?,?,?,?)");
        return $stmt->execute([$order_id,$method,$amount,$status]);
    }

    public function updateStatus($payment_id, $status) {
        $stmt = $this->conn->prepare("UPDATE payments SET status=? WHERE payment_id=?");
        return $stmt->execute([$status,$payment_id]);
    }

    public function delete($payment_id) {
        $stmt = $this->conn->prepare("DELETE FROM payments WHERE payment_id=?");
        return $stmt->execute([$payment_id]);
    }
}
