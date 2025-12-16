<?php
 //finished
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/InventoryDAO.php';


class InventoryService extends BaseService {

    public function __construct() {
        parent::__construct(new InventoryDAO());
    }

    public function get_inventory_for_product($productId) {
        return $this->dao->getByProductId($productId);
    }

    public function add_inventory_record($data) {
        if (!isset($data['product_id'], $data['change_type'], $data['quantity_change'])) {
            throw new Exception("product_id, change_type and quantity_change are required.");
        }
        return $this->dao->insert($data);
    }
}
