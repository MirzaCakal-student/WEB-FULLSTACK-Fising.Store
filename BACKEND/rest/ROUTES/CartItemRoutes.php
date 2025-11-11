<?php
require_once __DIR__ . '/../DAO/CartitemDAO.php';

$dao = new CartitemDAO();
$method = $_SERVER["REQUEST_METHOD"];
header("Content-Type: application/json");

switch ($method) {

    case "GET":
        if (isset($_GET["id"])) {
            echo json_encode([
                "message" => "Cart item fetched",
                "data" => $dao->getById($_GET["id"])
            ]);
        } elseif (isset($_GET["user_id"])) {
            echo json_encode([
                "message" => "User cart fetched",
                "data" => $dao->getUserCart($_GET["user_id"])
            ]);
        } else {
            echo json_encode([
                "message" => "Cart items fetched",
                "data" => $dao->getAll()
            ]);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        $id = $dao->insert($body);
        echo json_encode(["message" => "Cart item created", "id" => $id]);
        break;

    case "PUT":
        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $dao->update($_GET["id"], $body);
        echo json_encode(["message" => "Cart item updated", "success" => $ok]);
        break;

    case "DELETE":
        $ok = $dao->delete($_GET["id"]);
        echo json_encode(["message" => "Cart item removed", "success" => $ok]);
        break;
}
?>
