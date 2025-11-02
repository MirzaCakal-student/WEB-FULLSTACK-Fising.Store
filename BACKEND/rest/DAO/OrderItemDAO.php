<?php
require_once "Database.php";

class OrderItemDAO {
    private $conn;
    private $table = "order_items";

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function create($order_id, $product_id, $quantity, $price) {
        $sql = "INSERT INTO $this->table (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":order_id", $order_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":price", $price);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE order_item_id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
