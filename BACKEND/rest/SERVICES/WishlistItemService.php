<?php
 //finished
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/WishlistItemDAO.php';

class WishlistItemService extends BaseService {

    public function __construct() {
        parent::__construct(new WishlistItemDAO());
    }

   
    public function get_wishlist_by_user($userId) {
        return $this->dao->getByUserId($userId);
    }

    
    public function get_wishlist_items($userId) {
        return $this->get_wishlist_by_user($userId);
    }

  
    public function toggle_wishlist($data) {
        if (!isset($data['user_id'], $data['product_id'])) {
            throw new Exception("user_id and product_id required.");
        }

        $existing = $this->dao->getExistingWishlistItem($data['user_id'], $data['product_id']);
        if ($existing) {
            $this->dao->delete($existing['wishlist_item_id']);
            return ['removed' => true];
        } else {
            $id = $this->dao->insert([
                'user_id'    => $data['user_id'],
                'product_id' => $data['product_id']
            ]);
            return ['added' => true, 'id' => $id];
        }
    }

   
    public function add_wishlist_item($data) {
        return $this->toggle_wishlist($data);
    }

    public function delete_wishlist_item($id) {
        return $this->dao->delete($id);
    }
}
?>