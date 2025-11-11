<?php
require_once __DIR__ . '/../DAO/InventoryDAO.php';

$dao = new InventoryDAO();
$method = $_SERVER["REQUEST_METHOD"];
header("Content-Type: application/json");

switch ($method) {

    case "GET":
        if (isset($_GET["id"])) {
            echo json_encode([
                "message" => "Inventory record fetched",
                "data" => $dao->getById($_GET["id"])
            ]);
        } elseif (isset($_GET["product_id"])) {
            echo json_encode([
                "message" => "Inventory for product fetched",
                "data" => $dao->getByProduct($_GET["product_id"])
            ]);
        } else {
            echo json_encode([
                "message" => "All inventory records",
                "data" => $dao->getAll()
            ]);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        $id = $dao->insert($body);
        echo json_encode(["message" => "Inventory record created", "id" => $id]);
        break;

    case "PUT":
        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $dao->update($_GET["id"], $body);
        echo json_encode(["message" => "Inventory updated", "success" => $ok]);
        break;

    case "DELETE":
        $ok = $dao->delete($_GET["id"]);
        echo json_encode(["message" => "Inventory deleted", "success" => $ok]);
        break;
}
?>
