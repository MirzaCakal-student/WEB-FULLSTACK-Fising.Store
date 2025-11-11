<?php
require_once __DIR__ . '/../DAO/AddressDAO.php';

$dao = new AddressDAO();
$method = $_SERVER["REQUEST_METHOD"];

header("Content-Type: application/json");

switch ($method) {

    case "GET":
        if (isset($_GET["id"])) {
            echo json_encode([
                "message" => "Address fetched",
                "data" => $dao->getById($_GET["id"])
            ]);
        } elseif (isset($_GET["user_id"])) {
            echo json_encode([
                "message" => "Addresses for user fetched",
                "data" => $dao->getByUser($_GET["user_id"])
            ]);
        } else {
            echo json_encode([
                "message" => "All addresses",
                "data" => $dao->getAll()
            ]);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        $id = $dao->insert($body);
        echo json_encode(["message" => "Address created", "id" => $id]);
        break;

    case "PUT":
        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $dao->update($_GET["id"], $body);
        echo json_encode(["message" => "Address updated", "success" => $ok]);
        break;

    case "DELETE":
        $ok = $dao->delete($_GET["id"]);
        echo json_encode(["message" => "Address deleted", "success" => $ok]);
        break;
}
?>
