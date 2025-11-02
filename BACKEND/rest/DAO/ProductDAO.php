<?php
require_once "Database.php";

class ProductDAO {
    private $conn;
    private $table = "products";

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function create($name, $category, $price, $stock_quantity) {
        $sql = "INSERT INTO $this->table (name, category, price, stock_quantity)
                VALUES (:name, :category, :price, :stock_quantity)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":stock_quantity", $stock_quantity);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $category, $price, $stock_quantity) {
        $sql = "UPDATE $this->table SET name=:name, category=:category, price=:price, stock_quantity=:stock_quantity
                WHERE product_id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":stock_quantity", $stock_quantity);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE product_id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
