<?php
require_once __DIR__ . '/../DAO/PaymentDAO.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$dao = new PaymentDAO();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo json_encode($dao->getAll());
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode($dao->insert($data));
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
