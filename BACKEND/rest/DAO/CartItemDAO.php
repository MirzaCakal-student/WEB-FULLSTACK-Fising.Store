<?php
require_once "Database.php";

class CartItemDAO {
    private $conn;
    private $table = "cart_items";

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function create($user_id, $product_id, $quantity) {
        $sql = "INSERT INTO $this->table (user_id, product_id, quantity)
                VALUES (:user_id, :product_id, :quantity)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":quantity", $quantity);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE cart_item_id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
