<?php
require_once __DIR__ . '/Database.php';

class OrderItemDAO {
    private $conn;
    public function __construct() { $this->conn = Database::connect(); }

    public function getByOrder($order_id) {
        $stmt = $this->conn->prepare("SELECT oi.*, p.name, p.image_url 
                                      FROM order_items oi
                                      JOIN products p ON p.product_id=oi.product_id
                                      WHERE oi.order_id=?");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addItem($order_id, $product_id, $quantity, $price) {
        $stmt = $this->conn->prepare("INSERT INTO order_items(order_id,product_id,quantity,price) VALUES(?,?,?,?)");
        return $stmt->execute([$order_id,$product_id,$quantity,$price]);
    }

    public function delete($order_item_id) {
        $stmt = $this->conn->prepare("DELETE FROM order_items WHERE order_item_id=?");
        return $stmt->execute([$order_item_id]);
    }
}
