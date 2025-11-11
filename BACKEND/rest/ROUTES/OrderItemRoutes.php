<?php
require_once __DIR__ . '/../DAO/OrderitemDAO.php';

$dao = new OrderitemDAO();
$method = $_SERVER["REQUEST_METHOD"];
header("Content-Type: application/json");

switch ($method) {

    case "GET":
        if (isset($_GET["id"])) {
            echo json_encode([
                "message" => "Order item fetched",
                "data" => $dao->getById($_GET["id"])
            ]);
        } elseif (isset($_GET["order_id"])) {
            echo json_encode([
                "message" => "Order items for order fetched",
                "data" => $dao->getOrderItems($_GET["order_id"])
            ]);
        } else {
            echo json_encode([
                "message" => "All order items",
                "data" => $dao->getAll()
            ]);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        $id = $dao->insert($body);
        echo json_encode(["message" => "Order item created", "id" => $id]);
        break;

    case "PUT":
        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $dao->update($_GET["id"], $body);
        echo json_encode(["message" => "Order item updated", "success" => $ok]);
        break;

    case "DELETE":
        $ok = $dao->delete($_GET["id"]);
        echo json_encode(["message" => "Order item deleted", "success" => $ok]);
        break;
}
?>
