<?php
require_once 'BaseDAO.php';

class WishlistitemDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("wishlist_items");
    }

    public function getUserWishlist($userId) {
        $stmt = $this->connection->prepare("SELECT * FROM wishlist_items WHERE user_id = :id");
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
