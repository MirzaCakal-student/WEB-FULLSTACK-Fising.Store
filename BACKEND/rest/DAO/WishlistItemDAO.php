<?php
require_once __DIR__ . '/Database.php';

class WishlistItemDAO {
    private $conn;
    public function __construct() { $this->conn = Database::connect(); }

    public function getByUser($user_id) {
        $sql = "SELECT w.wishlist_item_id, p.product_id, p.name, p.category, p.price, p.image_url
                FROM wishlist_items w
                JOIN products p ON p.product_id=w.product_id
                WHERE w.user_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($user_id, $product_id) {
        $stmt = $this->conn->prepare("INSERT IGNORE INTO wishlist_items(user_id,product_id) VALUES(?,?)");
        return $stmt->execute([$user_id,$product_id]);
    }

    public function delete($wishlist_item_id) {
        $stmt = $this->conn->prepare("DELETE FROM wishlist_items WHERE wishlist_item_id=?");
        return $stmt->execute([$wishlist_item_id]);
    }
}
