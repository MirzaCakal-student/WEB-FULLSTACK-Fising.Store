<?php
require_once __DIR__ . '/BaseDao.php';

class WishlistItemDAO extends BaseDao {

    public function __construct() {
        // wishlist_items(wishlist_item_id, user_id, product_id, created_at)
        parent::__construct('wishlist_items', 'wishlist_item_id');
    }

    public function getByUserId($userId) {
        $sql = "SELECT wi.*, p.name, p.price, p.image_url
                FROM wishlist_items wi
                JOIN products p ON wi.product_id = p.product_id
                WHERE wi.user_id = :user_id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExistingWishlistItem($userId, $productId) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM wishlist_items
             WHERE user_id = :user_id AND product_id = :product_id"
        );
        $stmt->execute([
            ':user_id'    => $userId,
            ':product_id' => $productId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}