<?php
 //finished
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/OrderDAO.php';
require_once __DIR__ . '/../DAO/OrderItemDAO.php';
require_once __DIR__ . '/../DAO/CartItemDAO.php';
require_once __DIR__ . '/../DAO/ProductDAO.php';

class OrderService extends BaseService {

    private $orderItemDao;
    private $cartItemDao;
    private $productDao;

    public function __construct() {
        parent::__construct(new OrderDAO());
        $this->orderItemDao = new OrderItemDAO();
        $this->cartItemDao  = new CartItemDAO();
        $this->productDao   = new ProductDAO();
    }

    public function get_orders() {
        return $this->dao->getAll();
    }

    public function get_order_by_id($id) {
        $order = $this->dao->getById($id);
        if ($order) {
            $order['items'] = $this->orderItemDao->getByOrderId($id);
        }
        return $order;
    }

  
    public function create_order_from_cart($userId) {
        $cartItems = $this->cartItemDao->getByUserId($userId);
        if (empty($cartItems)) {
            throw new Exception("Cart is empty.");
        }

        $total = 0;
        foreach ($cartItems as $ci) {
            $total += $ci['price'] * $ci['quantity'];
        }

        $orderId = $this->dao->insert([
            'user_id'      => $userId,
            'total_amount' => $total,
            'status'       => 'pending'
        ]);

        foreach ($cartItems as $ci) {
            $this->orderItemDao->insert([
                'order_id'   => $orderId,
                'product_id' => $ci['product_id'],
                'quantity'   => $ci['quantity'],
                'price'      => $ci['price']
            ]);
        }

        return $this->get_order_by_id($orderId);
    }

    public function update_order($id, $data) {
        return $this->dao->update($id, $data);
    }

    public function delete_order($id) {
       
        $items = $this->orderItemDao->getByOrderId($id);
        foreach ($items as $item) {
            $this->orderItemDao->delete($item['order_item_id']);
        }
        return $this->dao->delete($id);
    }
}
?>
