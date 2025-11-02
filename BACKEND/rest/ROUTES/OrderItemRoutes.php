<?php
header("Content-Type: application/json");
require_once "../DAO/OrderItemDAO.php";

$dao = new OrderItemDAO();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($dao->readAll());
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->create($data['order_id'], $data['product_id'], $data['quantity'], $data['price']);
        echo json_encode(["message" => "Order item added"]);
        break;
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $dao->delete($data['order_item_id']);
        echo json_encode(["message" => "Order item deleted"]);
        break;
}
?>

