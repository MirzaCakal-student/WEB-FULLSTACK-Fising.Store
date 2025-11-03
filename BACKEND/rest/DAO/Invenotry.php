<?php
require_once __DIR__ . '/Database.php';

class InventoryDAO {
    private $conn;
    public function __construct() { $this->conn = Database::connect(); }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM inventory ORDER BY inventory_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByProduct($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM inventory WHERE product_id=?");
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addChange($product_id, $type, $quantity, $note=null) {
        $stmt = $this->conn->prepare("INSERT INTO inventory(product_id,change_type,quantity_change,note) VALUES(?,?,?,?)");
        return $stmt->execute([$product_id,$type,$quantity,$note]);
    }

    public function delete($inventory_id) {
        $stmt = $this->conn->prepare("DELETE FROM inventory WHERE inventory_id=?");
        return $stmt->execute([$inventory_id]);
    }
}
