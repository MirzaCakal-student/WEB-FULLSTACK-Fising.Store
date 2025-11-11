<?php
require_once 'BaseDAO.php';

class OrderitemDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("order_items");
    }

    public function getOrderItems($orderId) {
        $stmt = $this->connection->prepare("SELECT * FROM order_items WHERE order_id = :oid");
        $stmt->bindParam(":oid", $orderId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
