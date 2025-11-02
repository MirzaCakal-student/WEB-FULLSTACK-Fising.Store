<?php
header("Content-Type: application/json");
require_once "../DAO/AddressDAO.php";

$dao = new AddressDAO();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($dao->readAll());
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->create($data['user_id'], $data['full_name'], $data['street'], $data['city']);
        echo json_encode(["message" => "Address added successfully"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $dao->update($data['address_id'], $data['full_name'], $data['street'], $data['city']);
        echo json_encode(["message" => "Address updated"]);
        break;
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $dao->delete($data['address_id']);
        echo json_encode(["message" => "Address deleted"]);
        break;
}
?>
