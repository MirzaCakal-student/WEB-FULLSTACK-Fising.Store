<?php
require_once __DIR__ . '/../DAO/UserDAO.php';

$userDAO = new UserDAO();
$method = $_SERVER["REQUEST_METHOD"];

header("Content-Type: application/json");

switch ($method) {

    // GET ALL or GET BY ID
    case "GET":
        if (isset($_GET["id"])) {
            $data = $userDAO->getById($_GET["id"]);
            echo json_encode(["message" => "User fetched successfully", "data" => $data]);
        } else {
            $data = $userDAO->getAll();
            echo json_encode(["message" => "Users fetched successfully", "data" => $data]);
        }
        break;

    // CREATE
    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        $id = $userDAO->insert($body);
        echo json_encode(["message" => "User created", "id" => $id]);
        break;

    // UPDATE
    case "PUT":
        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $userDAO->update($_GET["id"], $body);
        echo json_encode(["message" => "User updated", "success" => $ok]);
        break;

    // DELETE
    case "DELETE":
        $ok = $userDAO->delete($_GET["id"]);
        echo json_encode(["message" => "User deleted", "success" => $ok]);
        break;
}
?>
