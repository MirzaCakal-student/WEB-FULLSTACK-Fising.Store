<?php
require_once "Database.php";

class InventoryDAO {
    private $conn;
    private $table = "inventory";

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function create($product_id, $change_type, $quantity_change) {
        $sql = "INSERT INTO $this->table (product_id, change_type, quantity_change)
                VALUES (:product_id, :change_type, :quantity_change)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":change_type", $change_type);
        $stmt->bindParam(":quantity_change", $quantity_change);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE inventory_id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
s