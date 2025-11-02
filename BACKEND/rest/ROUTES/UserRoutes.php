<?php
header("Content-Type: application/json");
require_once "../DAO/UserDAO.php";

$userDAO = new UserDAO();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($userDAO->readAll());
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $userDAO->create($data['username'], $data['email'], $data['password_hash'], $data['role']);
        echo json_encode(["message" => "User created successfully"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $userDAO->update($data['user_id'], $data['username'], $data['email'], $data['role']);
        echo json_encode(["message" => "User updated successfully"]);
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $userDAO->delete($data['user_id']);
        echo json_encode(["message" => "User deleted successfully"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request"]);
}
?>
