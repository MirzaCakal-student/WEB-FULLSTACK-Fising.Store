<?php
require_once 'BaseDAO.php';

class ProductDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("products");
    }
}
?>
