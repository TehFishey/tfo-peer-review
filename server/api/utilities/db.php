<?php
/************************************************************************************** 
 * PDO Utility Object
 * 
 * Description:
 * Simple PDO utility object. Used to open and manage database connections.
 **************************************************************************************/
include_once (__DIR__).'/../../config/config.php';

class Database {
    // specify your own credentials
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    public $conn;
  
    // get the database connection
    public function getConnection() {
  
        $this->conn = null;
  
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>