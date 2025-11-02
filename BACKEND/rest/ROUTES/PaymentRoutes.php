<?php
header("Content-Type: application/json");
require_once "../DAO/PaymentDAO.php";

$dao = new PaymentDAO();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($dao->readAll());
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->create($data['order_id'], $data['payment_method'], $data['amount'], $data['status']);
        echo json_encode(["message" => "Payment created"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->updateStatus($data['payment_id'], $data['status']);
        echo json_encode(["message" => "Payment updated"]);
        break;
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $dao->delete($data['payment_id']);
        echo json_encode(["message" => "Payment deleted"]);
        break;
}
?>
