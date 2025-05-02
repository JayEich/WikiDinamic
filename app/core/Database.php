<?php
namespace Core;
use PDO;
use PDOException;
class Database {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            $host = $_ENV['DB_HOST'];
            $db   = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

            try {
                self::$conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}
