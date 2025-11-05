<?php
require_once __DIR__ . '/../DAO/OrderDAO.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$dao = new OrderDAO();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['user_id'])) {
            echo json_encode($dao->getByUserId($_GET['user_id']));
        } else {
            echo json_encode($dao->getAll());
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode($dao->insert($data));
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        echo json_encode($dao->delete($id));
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
