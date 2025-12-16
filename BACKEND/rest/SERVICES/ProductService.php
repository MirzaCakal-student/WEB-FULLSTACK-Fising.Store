<?php
 //finished
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/ProductDAO.php';

class ProductService extends BaseService {

    public function __construct() {
        parent::__construct(new ProductDAO());
    }

    public function get_products() {
        return $this->dao->getAll();
    }

    public function get_product_by_id($id) {
        return $this->dao->getById($id);
    }

    /**
     * Glavna metoda koju rute koriste â€“ POST /products
     */
    public function add_product($data) {
        // Business logic / validation
        if (empty($data['name'])) {
            throw new Exception("Product name is required.");
        }

        if (empty($data['category'])) {
            throw new Exception("Product category is required.");
        }

        if (!isset($data['price']) || $data['price'] < 0) {
            throw new Exception("Price must be 0 or positive.");
        }

        if (!isset($data['stock_quantity']) || $data['stock_quantity'] < 0) {
            $data['stock_quantity'] = 0;
        }

        if (!isset($data['image_url'])) {
            $data['image_url'] = null;
        }

        return $this->dao->insert($data);
    }

    public function create_product($data) {
        return $this->add_product($data);
    }

    public function update_product($id, $data) {
        if (isset($data['price']) && $data['price'] < 0) {
            throw new Exception("Price must be 0 or positive.");
        }

        if (isset($data['stock_quantity']) && $data['stock_quantity'] < 0) {
            throw new Exception("Stock quantity cannot be negative.");
        }

        return $this->dao->update($id, $data);
    }

    public function delete_product($id) {
        return $this->dao->delete($id);
    }
}
?>