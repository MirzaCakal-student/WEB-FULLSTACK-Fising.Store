<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/CartItemDAO.php';
require_once __DIR__ . '/../DAO/ProductDAO.php';

class CartItemService extends BaseService {

    private $productDao;

    public function __construct() {
        parent::__construct(new CartItemDAO());
        $this->productDao = new ProductDAO();
    }

 
    public function get_cart_by_user($userId) {
        return $this->dao->getByUserId($userId);
    }

   
    public function get_cart_items($userId) {
        return $this->get_cart_by_user($userId);
    }

   
    public function add_to_cart($data) {
        if (!isset($data['user_id'], $data['product_id'])) {
            throw new Exception("user_id and product_id are required.");
        }

        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
        if ($quantity < 1) $quantity = 1;

        $product = $this->productDao->getById($data['product_id']);
        if (!$product) {
            throw new Exception("Product not found.");
        }

        // check if exists
        $existing = $this->dao->getExistingCartItem($data['user_id'], $data['product_id']);
        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            return $this->dao->update($existing['cart_item_id'], ['quantity' => $newQty]);
        } else {
            return $this->dao->insert([
                'user_id'    => $data['user_id'],
                'product_id' => $data['product_id'],
                'quantity'   => $quantity,
            ]);
        }
    }

   
    public function add_cart_item($data) {
        return $this->add_to_cart($data);
    }

    public function update_cart_item($id, $data) {
        if (isset($data['quantity']) && $data['quantity'] < 1) {
            throw new Exception("Quantity must be at least 1.");
        }
        return $this->dao->update($id, $data);
    }

    public function delete_cart_item($id) {
        return $this->dao->delete($id);
    }
}
?>
