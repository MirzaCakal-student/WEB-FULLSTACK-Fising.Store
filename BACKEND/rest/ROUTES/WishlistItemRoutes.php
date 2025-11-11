<?php
require_once __DIR__ . '/../DAO/WishlistitemDAO.php';

$dao = new WishlistitemDAO();
$method = $_SERVER["REQUEST_METHOD"];

header("Content-Type: application/json");

switch ($method) {

    case "GET":
        if (isset($_GET["id"])) {
            echo json_encode([
                "message" => "Wishlist item fetched",
                "data" => $dao->getById($_GET["id"])
            ]);
        } elseif (isset($_GET["user_id"])) {
            echo json_encode([
                "message" => "User wishlist fetched",
                "data" => $dao->getUserWishlist($_GET["user_id"])
            ]);
        } else {
            echo json_encode([
                "message" => "Wishlist items fetched",
                "data" => $dao->getAll()
            ]);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        $id = $dao->insert($body);
        echo json_encode(["message" => "Wishlist item added", "id" => $id]);
        break;

    case "PUT":
        $body = json_decode(file_get_contents("php://input"), true);
        $ok = $dao->update($_GET["id"], $body);
        echo json_encode(["message" => "Wishlist updated", "success" => $ok]);
        break;

    case "DELETE":
        $ok = $dao->delete($_GET["id"]);
        echo json_encode(["message" => "Wishlist item removed", "success" => $ok]);
        break;
}
?>
