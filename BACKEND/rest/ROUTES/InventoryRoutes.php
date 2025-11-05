<?php
header("Content-Type: application/json");
require_once "../DAO/InventoryDAO.php";

$dao = new InventoryDAO();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($dao->readAll());
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->create($data['product_id'], $data['change_type'], $data['quantity_change']);
        echo json_encode(["message" => "Inventory record added"]);
        break;
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $dao->delete($data['inventory_id']);
        echo json_encode(["message" => "Inventory record deleted"]);
        break;
}
?>
