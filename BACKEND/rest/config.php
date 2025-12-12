<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Config {
    public static function DB_NAME() {
        return 'fishingplanet';
    }
    
    public static function DB_PORT() {
        return 3306;
    }
    
    public static function DB_USER() {
        return 'root';
    }
    
    public static function DB_PASSWORD() {
        return '';
    }
    
    public static function DB_HOST() {
        return '127.0.0.1';
    }

    // JWT Secret Key - IMPORTANT: Change this to a strong random string
    public static function JWT_SECRET() {
        return 'B8B8B8B88B8B8B88B8BB88BB88B8B8';
    }
}

class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . Config::DB_HOST() . ";dbname=" . Config::DB_NAME() . ";port=" . Config::DB_PORT(),
                    Config::DB_USER(),
                    Config::DB_PASSWORD(),
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
?>