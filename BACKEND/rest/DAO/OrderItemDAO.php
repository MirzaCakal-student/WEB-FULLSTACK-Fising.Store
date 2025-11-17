<?php
require_once __DIR__ . '/BaseDao.php';

class OrderItemDAO extends BaseDao {

    public function __construct() {
        // order_items(order_item_id, order_id, product_id, quantity, price)
        parent::__construct('order_items', 'order_item_id');
    }

    public function getByOrderId($orderId) {
        $sql = "SELECT oi.*, p.name, p.image_url
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = :order_id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
