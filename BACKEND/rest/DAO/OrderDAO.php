<?php
require_once __DIR__ . '/Database.php';

class OrderDAO {
    private $conn;
    public function __construct() { $this->conn = Database::connect(); }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($user_id, $total_amount, $payment_status='unpaid') {
        $stmt = $this->conn->prepare("INSERT INTO orders(user_id,total_amount,payment_status) VALUES(?,?,?)");
        $stmt->execute([$user_id,$total_amount,$payment_status]);
        return $this->conn->lastInsertId();
    }

    public function updateStatus($order_id, $status) {
        $stmt = $this->conn->prepare("UPDATE orders SET payment_status=? WHERE order_id=?");
        return $stmt->execute([$status,$order_id]);
    }

    public function delete($order_id) {
        $stmt = $this->conn->prepare("DELETE FROM orders WHERE order_id=?");
        return $stmt->execute([$order_id]);
    }
}
