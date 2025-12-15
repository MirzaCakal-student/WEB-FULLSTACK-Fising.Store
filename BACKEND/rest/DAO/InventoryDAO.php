<?php
 //finished
// BACKEND/rest/DAO/InventoryDAO.php

require_once __DIR__ . '/BaseDao.php';

class InventoryDAO extends BaseDao {

    public function __construct() {
        // tabela: inventory(inventory_id, product_id, change_type, quantity_change, note, created_at)
        parent::__construct('inventory', 'inventory_id');
    }

    public function getByProductId($productId) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM inventory WHERE product_id = :product_id"
        );
        $stmt->bindValue(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
