<?php
 //finished
require_once __DIR__ . '/BaseDao.php';

class OrderDAO extends BaseDao {

    public function __construct() {
        // orders(order_id, user_id, total_amount, status, created_at)
        parent::__construct('orders', 'order_id');
    }

    public function getByUserId($userId) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM orders WHERE user_id = :user_id"
        );
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
