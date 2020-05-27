<?php
class Session {
    // database connection and table name
    private $conn;
    private $table_name = "tbl_sessions";
  
    // object properties
    public $sessionId;      // integer - auto-incrementing unique identifier for session
    public $ip;             // string - 45 character ipv4 or ipv6 for associated user
    public $time;           // string - 10 character Unix timestamp session's last update
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function updateAndRead() {
        $query = "INSERT INTO ".$this->table_name."(ip, time) VALUES(:ip, :time) 
            ON DUPLICATE KEY UPDATE time=:uptime";
        $stmt = $this->conn->prepare($query);
        
        
        $this->ip=htmlspecialchars(strip_tags($this->ip));
        $this->time=htmlspecialchars(strip_tags($this->time));
        $stmt->bindParam(":ip", $this->ip);
        $stmt->bindParam(":time", $this->time);
        $stmt->bindParam(":uptime", $this->time);
        $stmt->execute();


        $query = "SELECT s.sessionId, s.ip, s.time 
            FROM ".$this->table_name. " AS s
            WHERE s.ip=:ip";
        $stmt = $this->conn->prepare($query);

        $this->ip=htmlspecialchars(strip_tags($this->ip));
        $stmt->bindParam(":ip", $this->ip);
        $stmt->execute();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->sessionId = $row['sessionId'];
            $this->ip = $row['ip'];
            $this->time = $row['time'];
        }
    }
}
?>