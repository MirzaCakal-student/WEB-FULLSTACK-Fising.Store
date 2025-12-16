<?php
 //finished
require_once __DIR__ . '/BaseDao.php';

class ProductDAO extends BaseDao {

    public function __construct() {
        // products(product_id, name, category, price, stock_quantity, image_url, created_at)
        parent::__construct('products', 'product_id');
    }

    public function getByCategory($category) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM products WHERE category = :category"
        );
        $stmt->bindValue(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>