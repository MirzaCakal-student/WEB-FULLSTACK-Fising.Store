<?php
require_once 'BaseDAO.php';

class OrderDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("orders");
    }

    public function getByUser($userId) {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE user_id = :uid");
        $stmt->bindParam(":uid", $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
