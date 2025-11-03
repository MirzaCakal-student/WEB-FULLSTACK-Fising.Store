<?php
require_once("../../DAO/ProductDAO.php");
header("Content-Type: application/json");
$dao = new ProductDAO();

switch($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    echo json_encode($dao->readAll());
    break;

  case "POST":
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($dao->create($data));
    break;

  case "PUT":
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($dao->update($data));
    break;

  case "DELETE":
    $id = $_GET["id"] ?? null;
    echo json_encode($dao->delete($id));
    break;
}
?>
