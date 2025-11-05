<?php
require_once("../../DAO/CartItemDAO.php");
header("Content-Type: application/json");
$dao = new CartItemDAO();

switch($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    $userId = $_GET["user_id"] ?? null;
    echo json_encode($dao->readAll($userId));
    break;

  case "POST":
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($dao->create($data));
    break;

  case "DELETE":
    $id = $_GET["id"] ?? null;
    echo json_encode($dao->delete($id));
    break;
}
?>
