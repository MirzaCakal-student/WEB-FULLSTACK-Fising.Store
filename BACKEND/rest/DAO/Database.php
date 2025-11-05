<?php
class Database {
    private static $conn;

    public static function connect() {
        if (!self::$conn) {
            $host = "localhost";
            $db   = "fishingplanet";
            $user = "root";      
            $pass = "";          

            self::$conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }
}
