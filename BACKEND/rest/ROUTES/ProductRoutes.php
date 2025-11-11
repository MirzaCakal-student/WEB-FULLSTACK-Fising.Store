<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../DAO/ProductDAO.php';

$dao = new ProductDAO();
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":
        if (isset($_GET["id"])) {
            echo json_encode([
                "success" => true,
                "message" => "Product fetched",
                "data" => $dao->getById($_GET["id"])
            ]);
        } else {
            echo json_encode([
                "success" => true,
                "message" => "All products fetched",
                "data" => $dao->getAll()
            ]);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        $id = $dao->insert($body);
        echo json_encode([
            "success" => true,
            "message" => "Product created",
            "id" => $id
        ]);
        break;

    case "PUT":
        if (!isset($_GET["id"])) {
            echo json_encode(["success" => false, "message" => "Missing ID"]);
            exit;
        }

        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $dao->update($_GET["id"], $body);
        echo json_encode([
            "success" => $ok,
            "message" => "Product updated"
        ]);
        break;

    case "DELETE":
        if (!isset($_GET["id"])) {
            echo json_encode(["success" => false, "message" => "Missing ID"]);
            exit;
        }

        $ok = $dao->delete($_GET["id"]);
        echo json_encode([
            "success" => $ok,
            "message" => "Product deleted"
        ]);
        break;
}
?>
