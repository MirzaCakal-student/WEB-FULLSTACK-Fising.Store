<?php
require_once 'BaseDAO.php';

class AddressDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("addresses");
    }

    public function getByUser($userId) {
        $stmt = $this->connection->prepare("SELECT * FROM addresses WHERE user_id = :id");
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
