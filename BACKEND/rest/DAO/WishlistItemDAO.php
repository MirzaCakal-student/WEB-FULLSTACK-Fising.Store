<?php
require_once "Database.php";

class WishlistItemDAO {
    private $conn;
    private $table = "wishlist_items";

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function create($user_id, $product_id) {
        $sql = "INSERT INTO $this->table (user_id, product_id)
                VALUES (:user_id, :product_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":product_id", $product_id);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE wishlist_item_id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
