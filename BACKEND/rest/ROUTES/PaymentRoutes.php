<?php
require_once __DIR__ . '/../DAO/PaymentDAO.php';

$dao = new PaymentDAO();
$method = $_SERVER["REQUEST_METHOD"];
header("Content-Type: application/json");

switch ($method) {

    case "GET":
        if (isset($_GET["id"])) {
            echo json_encode([
                "message" => "Payment fetched",
                "data" => $dao->getById($_GET["id"])
            ]);
        } elseif (isset($_GET["order_id"])) {
            echo json_encode([
                "message" => "Payments for order fetched",
                "data" => $dao->getByOrder($_GET["order_id"])
            ]);
        } else {
            echo json_encode([
                "message" => "All payments",
                "data" => $dao->getAll()
            ]);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        $id = $dao->insert($body);
        echo json_encode(["message" => "Payment created", "id" => $id]);
        break;

    case "PUT":
        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $dao->update($_GET["id"], $body);
        echo json_encode(["message" => "Payment updated", "success" => $ok]);
        break;

    case "DELETE":
        $ok = $dao->delete($_GET["id"]);
        echo json_encode(["message" => "Payment deleted", "success" => $ok]);
        break;
}
?>
