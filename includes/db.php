<?php 

class Database {
    private $hostname = "localhost";
    private $username = "root";
    private $password = "devola3465";
    private $database = "db_oop";
    private $connection;

    public function __construct(){
        $this->connect();
        date_default_timezone_set("Asia/Jakarta");
    }

    private function connect(){
        $this->connection = new mysqli($this->hostname, $this->username, $this->password, $this->database);
        if($this->connection->connect_error){
            echo "Koneksi database rusak";
            die ("Error: " .  $this->connection->connect_error);
        }
    }
    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

$db = new Database();
$conn = $db->getConnection();
?>