<?php
require_once 'LoadEnv.php';

class Database
{
    private static $instance = null;
    private $pdo;

    private $host     = "localhost";
    private $username = "root";
    private $password = "";
    // Write database name here
    private $dbname = "";

    // Private constructor → Singleton
    private function __construct()
    {
        try {
            LoadEnv::load(__DIR__ . '/../.env');

            $host     = $_ENV['DB_HOST'];
            $user     = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];
            $dbname   = $_ENV['DB_NAME'];

            $this->pdo = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8",
                $user,
                $password
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    // Public static getter → always returns same instance
    public static function getInstance()
    {

        if (self::$instance == null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    // Getter for PDO
    public function getConnection()
    {
        return $this->pdo;
    }
}
