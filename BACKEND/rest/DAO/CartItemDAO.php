<?php
require_once __DIR__ . '/Database.php';

class CartItemDAO {
    private $conn;
    public function __construct() { $this->conn = Database::connect(); }

    public function getByUser($user_id) {
        $sql = "SELECT ci.cart_item_id, ci.quantity, p.product_id, p.name, p.price, p.image_url, 
                       (p.price * ci.quantity) AS total
                FROM cart_items ci
                JOIN products p ON p.product_id = ci.product_id
                WHERE ci.user_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($user_id, $product_id, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO cart_items(user_id,product_id,quantity) VALUES(?,?,?) 
                                      ON DUPLICATE KEY UPDATE quantity=quantity+VALUES(quantity)");
        $stmt->execute([$user_id,$product_id,$quantity]);
        return $this->conn->lastInsertId();
    }

    public function updateQuantity($cart_item_id, $quantity) {
        $stmt = $this->conn->prepare("UPDATE cart_items SET quantity=? WHERE cart_item_id=?");
        return $stmt->execute([$quantity,$cart_item_id]);
    }

    public function delete($cart_item_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart_items WHERE cart_item_id=?");
        return $stmt->execute([$cart_item_id]);
    }

    public function getTotal($user_id) {
        $stmt = $this->conn->prepare("SELECT COALESCE(SUM(p.price * ci.quantity),0) AS total 
                                      FROM cart_items ci JOIN products p ON p.product_id=ci.product_id 
                                      WHERE ci.user_id=?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
