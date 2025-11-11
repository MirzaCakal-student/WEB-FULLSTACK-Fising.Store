<?php
require_once 'BaseDAO.php';

class PaymentDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("payments");
    }

    public function getByOrder($orderId) {
        $stmt = $this->connection->prepare("SELECT * FROM payments WHERE order_id = :oid");
        $stmt->bindParam(":oid", $orderId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
