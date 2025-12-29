<?php
// Config fajl prilagođen za AlwaysData na osnovu tvojih slika
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Config {
    public static function DB_HOST() {
        // Tačan host sa tvoje slike (žuti okvir)
        return 'mysql-fishingstore.alwaysdata.net'; 
    }

    public static function DB_NAME() {
        // Ime tvoje importovane baze
        return 'fishingstore_ibu'; 
    }
    
    public static function DB_USER() {
        // Tvoj kreirani korisnik sa slike br. 2
        return '448787_mirza'; 
    }
    
    public static function DB_PASSWORD() {
    
        return '2005Mirza'; 
    }
    
    public static function DB_PORT() {
        return 3306;
    }

    // JWT Secret (ostaje isti)
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
                // Ovo će ispisati grešku ako se ne uspije povezati
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
?>