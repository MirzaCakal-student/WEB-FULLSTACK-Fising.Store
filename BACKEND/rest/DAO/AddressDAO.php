<?php
require_once "Database.php";

class AddressDAO {
    private $conn;
    private $table = "addresses";

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function create($user_id, $full_name, $street, $city) {
        $sql = "INSERT INTO $this->table (user_id, full_name, street, city)
                VALUES (:user_id, :full_name, :street, :city)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":full_name", $full_name);
        $stmt->bindParam(":street", $street);
        $stmt->bindParam(":city", $city);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $full_name, $street, $city) {
        $sql = "UPDATE $this->table SET full_name=:full_name, street=:street, city=:city WHERE address_id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":full_name", $full_name);
        $stmt->bindParam(":street", $street);
        $stmt->bindParam(":city", $city);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE address_id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
