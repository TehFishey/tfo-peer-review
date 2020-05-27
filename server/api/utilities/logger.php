<?php
class Logger {

    private $conn;
    private $table_name = "Log_Weekly";
    
    public $ip;             // string - 45 character ipv4 or ipv6 address
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    private function log($action) {
        $query = "INSERT INTO ".$this->table_name."(ip, action, count) 
            SELECT :ip, :action, 1
            ON DUPLICATE KEY UPDATE count = count + 1";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ip", $this->ip);
        $stmt->bindParam(":action", $action);
        if($stmt->execute()) {return true;}
        return false;
    }

    function logView() {
        return $this->log("view");
    }
    function logClick() {
        return $this->log("click");
    }
    function logCurl() {
        return $this->log("curl");
    }
    function logAdd() {
        return $this->log("add");
    }
    function logRemove() {
        return $this->log("remove");
    }
}


?>