<?php
require_once 'BaseDAO.php';

class CartitemDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("cart_items");
    }

    public function getUserCart($userId) {
        $stmt = $this->connection->prepare("SELECT * FROM cart_items WHERE user_id = :id");
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
