<?php
header("Content-Type: application/json");
require_once "../DAO/CartItemDAO.php";

$dao = new CartItemDAO();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($dao->readAll());
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->create($data['user_id'], $data['product_id'], $data['quantity']);
        echo json_encode(["message" => "Item added to cart"]);
        break;
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $dao->delete($data['cart_item_id']);
        echo json_encode(["message" => "Cart item deleted"]);
        break;
}
?>
