<?php
/************************************************************************************** 
 * Logger Utility Object
 * 
 * Description:
 * A rudimentary logger object, used to assist in various logging operations and internal metrics
 * collection. Metrics are written to the "Log_Weekly" table, tracking ip's and their associated 
 * numbers of clicks, cURL requests, creature/create actions, and creature/delete actions. Every week,
 * these values are compounded into single entries in the "Log_Compounded" table by the 'cron-weekly'
 * cron event; "Log_Weekly" is then cleared.
 **************************************************************************************/
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
        return $this->log("creatureAdd");
    }
    function logRemove() {
        return $this->log("creatureRemove");
    }
}


?>