<?php
require_once 'BaseDAO.php';

class InventoryDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("inventory");
    }

    public function getByProduct($productId) {
        $stmt = $this->connection->prepare("SELECT * FROM inventory WHERE product_id = :pid");
        $stmt->bindParam(":pid", $productId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
