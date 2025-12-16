<?php
 //finished
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/AddressDAO.php';

class AddressService extends BaseService {

    public function __construct() {
        parent::__construct(new AddressDAO());
    }

    public function get_addresses_by_user($userId) {
        return $this->dao->getByUserId($userId);
    }

    public function add_address($data) {
        if (!isset($data['user_id'], $data['full_name'], $data['street'], $data['city'])) {
            throw new Exception("user_id, full_name, street and city are required.");
        }

        return $this->dao->insert($data);
    }

    public function update_address($id, $data) {
        return $this->dao->update($id, $data);
    }

    public function delete_address($id) {
        return $this->dao->delete($id);
    }
}
