<?php
require_once __DIR__ . '/BaseDao.php';

class PaymentDAO extends BaseDao {

    public function __construct() {
        // payments(payment_id, order_id, amount, method, status, created_at)
        parent::__construct('payments', 'payment_id');
    }

    public function getByOrderId($orderId) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM payments WHERE order_id = :order_id"
        );
        $stmt->bindValue(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
