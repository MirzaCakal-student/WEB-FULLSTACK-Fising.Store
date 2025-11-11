<?php
require_once __DIR__ . '/../DAO/OrderDAO.php';

$dao = new OrderDAO();
$method = $_SERVER["REQUEST_METHOD"];

header("Content-Type: application/json");

switch ($method) {

    case "GET":
        if (isset($_GET["id"])) {
            echo json_encode([
                "message" => "Order fetched",
                "data" => $dao->getById($_GET["id"])
            ]);
        } elseif (isset($_GET["user_id"])) {
            echo json_encode([
                "message" => "Orders by user fetched",
                "data" => $dao->getByUser($_GET["user_id"])
            ]);
        } else {
            echo json_encode([
                "message" => "All orders fetched",
                "data" => $dao->getAll()
            ]);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        $id = $dao->insert($body);
        echo json_encode(["message" => "Order created", "id" => $id]);
        break;

    case "PUT":
        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $dao->update($_GET["id"], $body);
        echo json_encode(["message" => "Order updated", "success" => $ok]);
        break;

    case "DELETE":
        $ok = $dao->delete($_GET["id"]);
        echo json_encode(["message" => "Order deleted", "success" => $ok]);
        break;
}
?>
