<?php
header("Content-Type: application/json");
require_once "../DAO/ProductDAO.php";

$dao = new ProductDAO();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($dao->readAll());
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->create($data['name'], $data['category'], $data['price'], $data['stock_quantity']);
        echo json_encode(["message" => "Product added successfully"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->update($data['product_id'], $data['name'], $data['category'], $data['price'], $data['stock_quantity']);
        echo json_encode(["message" => "Product updated"]);
        break;
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $dao->delete($data['product_id']);
        echo json_encode(["message" => "Product deleted"]);
        break;
}
?>
