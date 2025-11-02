<?php
header("Content-Type: application/json");
require_once "../DAO/OrderDAO.php";

$dao = new OrderDAO();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($dao->readAll());
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->create($data['user_id'], $data['total_amount'], $data['payment_status']);
        echo json_encode(["message" => "Order created successfully"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->updateStatus($data['order_id'], $data['payment_status']);
        echo json_encode(["message" => "Order status updated"]);
        break;
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $dao->delete($data['order_id']);
        echo json_encode(["message" => "Order deleted"]);
        break;
}
?>
