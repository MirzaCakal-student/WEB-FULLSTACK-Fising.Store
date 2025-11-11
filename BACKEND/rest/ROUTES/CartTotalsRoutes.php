<?php
require_once __DIR__ . '/../config.php';

$conn = Database::connect();
$method = $_SERVER["REQUEST_METHOD"];
header("Content-Type: application/json");

if ($method === "GET") {
    if (isset($_GET["user_id"])) {
        $stmt = $conn->prepare("SELECT * FROM v_cart_totals WHERE user_id = :uid");
        $stmt->bindParam(":uid", $_GET["user_id"]);
        $stmt->execute();

        echo json_encode([
            "message" => "Cart total for user fetched",
            "data" => $stmt->fetch()
        ]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM v_cart_totals");
        $stmt->execute();

        echo json_encode([
            "message" => "All cart totals fetched",
            "data" => $stmt->fetchAll()
        ]);
    }
}
?>
