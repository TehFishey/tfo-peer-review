<?php
/************************************************************************************** 
 * Main Data Object for "Session" table.
 * Primary Key: sessionID
 * Unique Key: ip
 * 
 * Description:
 * The "Sessions" table tracks $ip based user sessions, keyed to an auto-incrementing $sessionId, as well as a 
 * timestamp ($time) for when the session was last updated or accessed. Ip-based sessions are used to validate
 * frontend interactions with various endpoints associated with the "SessionCache" and "Creatures" table. Sessions
 * more than 10 minutes old are periodically cleared by the 'flush-db' cron event.
 * 
 * Methods:
 * ->updateAndRead() - Used as a "SELECT if exists, otherwise INSERT" command for the "Sessions" table. Opens
 *                     a new session if none exists matching $this->ip, updates the 'time' field if one DOES 
 *                     exist, and then maps the new/updated session's fields to the data object's properties.
 *                     This method is used for validation purposes by numerous endpoints. 
 *                       Requires: $this->ip, $this->time
 * 
 **************************************************************************************/
class Session {
    // database connection and table name
    private $conn;
    private $table_name = "Sessions";
  
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