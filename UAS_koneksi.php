<?php
require_once 'env.php';

class Database {
    private $host;
    private $port;
    private $username;
    private $password;
    private $db_name;
    protected $conn;

    public function __construct() {
        $this->host     = $_ENV['DB_HOST']  ?? 'localhost';
        $this->port     = $_ENV['DB_PORT']  ?? '5432';
        $this->username = $_ENV['DB_USER']  ?? '';
        $this->password = $_ENV['DB_PASS']  ?? '';
        $this->db_name  = $_ENV['DB_NAME']  ?? '';
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET NAMES 'UTF8'");
        } catch (PDOException $e) {
            echo "Koneksi Gagal: " . $e->getMessage();
        }
        return $this->conn;
    }
}
?>