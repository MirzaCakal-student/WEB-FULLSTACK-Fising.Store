<?php
require_once __DIR__ . '/Database.php';

class ProductDAO {
    private $conn;
    public function __construct() { $this->conn = Database::connect(); }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM products ORDER BY product_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE product_id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $category, $price, $stock_quantity, $image_url=null) {
        $stmt = $this->conn->prepare("INSERT INTO products(name,category,price,stock_quantity,image_url) VALUES(?,?,?,?,?)");
        $stmt->execute([$name,$category,$price,$stock_quantity,$image_url]);
        return $this->conn->lastInsertId();
    }

    public function update($id, $name, $category, $price, $stock_quantity, $image_url) {
        $stmt = $this->conn->prepare("UPDATE products SET name=?, category=?, price=?, stock_quantity=?, image_url=? WHERE product_id=?");
        return $stmt->execute([$name,$category,$price,$stock_quantity,$image_url,$id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE product_id=?");
        return $stmt->execute([$id]);
    }
}
