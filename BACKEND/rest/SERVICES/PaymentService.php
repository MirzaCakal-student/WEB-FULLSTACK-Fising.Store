<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/PaymentDAO.php';

class PaymentService extends BaseService {

    public function __construct() {
        parent::__construct(new PaymentDAO());
    }

    public function get_payments() {
        return $this->dao->getAll();
    }

    public function get_payment_by_id($id) {
        return $this->dao->getById($id);
    }

    public function get_payments_by_order($orderId) {
        return $this->dao->getByOrderId($orderId);
    }

    public function add_payment($data) {
        if (!isset($data['order_id'], $data['amount'], $data['method'])) {
            throw new Exception("order_id, amount and method are required.");
        }
        if ($data['amount'] <= 0) {
            throw new Exception("Amount must be positive.");
        }
        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }

        return $this->dao->insert($data);
    }

    public function update_payment($id, $data) {
        return $this->dao->update($id, $data);
    }

    public function delete_payment($id) {
        return $this->dao->delete($id);
    }
}
