<?php
require_once __DIR__ . '/BaseDao.php';

class CartItemDAO extends BaseDao {

    public function __construct() {
        // cart_items(cart_item_id, user_id, product_id, quantity, created_at)
        parent::__construct('cart_items', 'cart_item_id');
    }

    public function getByUserId($userId) {
        $sql = "SELECT ci.*, p.name, p.price, p.image_url
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.product_id
                WHERE ci.user_id = :user_id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExistingCartItem($userId, $productId) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM cart_items 
             WHERE user_id = :user_id AND product_id = :product_id"
        );
        $stmt->execute([
            ':user_id'    => $userId,
            ':product_id' => $productId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>