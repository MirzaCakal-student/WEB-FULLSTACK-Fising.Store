<?php
require_once __DIR__ . '/BaseDao.php';

class AddressDAO extends BaseDao {

    public function __construct() {
        // addresses(address_id, user_id, full_name, street, city, created_at)
        parent::__construct('addresses', 'address_id');
    }

    public function getByUserId($userId) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM addresses WHERE user_id = :user_id"
        );
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
