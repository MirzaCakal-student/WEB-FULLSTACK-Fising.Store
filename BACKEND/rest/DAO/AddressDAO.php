<?php
require_once __DIR__ . '/Database.php';

class AddressDAO {
    private $conn;
    public function __construct() { $this->conn = Database::connect(); }

    public function getByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM addresses WHERE user_id=?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($user_id, $full_name, $street, $city) {
        $stmt = $this->conn->prepare("INSERT INTO addresses(user_id,full_name,street,city) VALUES(?,?,?,?)");
        $stmt->execute([$user_id,$full_name,$street,$city]);
        return $this->conn->lastInsertId();
    }

    public function update($address_id, $full_name, $street, $city) {
        $stmt = $this->conn->prepare("UPDATE addresses SET full_name=?, street=?, city=? WHERE address_id=?");
        return $stmt->execute([$full_name,$street,$city,$address_id]);
    }

    public function delete($address_id) {
        $stmt = $this->conn->prepare("DELETE FROM addresses WHERE address_id=?");
        return $stmt->execute([$address_id]);
    }
}
