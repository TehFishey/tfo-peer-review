<?php
/************************************************************************************** 
 * Logger Utility Object
 * 
 * Description:
 * A rudimentary data logger, used to assist in various logging operations and internal metrics
 * collection. Metrics are written to the "Log_Metrics" table, and are incremented based on a weekId field
 * (form 'Year'-'WeekOfYear', or YYYY-WW). Additionally, unique creature codes and user labname strings are written to
 * the Log_Creatures and Log_Labs tables respectively, again adjoined by weekIds.
 *
 * User IP addresses are logged separately, in the Log_Uniques table. This table is
 * cleared every week by the 'cron-weekly' cron event.
 * 
 **************************************************************************************/
class Logger {

    private $conn;
    private $metrics_table_name = "Log_Metrics";
    private $creatures_table_name = "Log_Creatures";
    private $labs_table_name = "Log_Labs";
    private $ips_table_name = "Log_Uniques";

    public $ip;                     // string - 45 character ipv4 or ipv6 address
    public $weekId;                 // 
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    private function logMetric($action) {
        if($action === 'click') {
            $query = "INSERT INTO ".$this->metrics_table_name."(weekId, clicks) 
            SELECT :weekId, 1
            ON DUPLICATE KEY UPDATE clicks = clicks + 1";
        }
        else if($action === 'curl') {
            $query = "INSERT INTO ".$this->metrics_table_name."(weekId, curls) 
            SELECT :weekId, 1
            ON DUPLICATE KEY UPDATE curls = curls + 1";
        }
        else if($action === 'creatureAdd') {
            $query = "INSERT INTO ".$this->metrics_table_name."(weekId, creatureAdds) 
            SELECT :weekId, 1
            ON DUPLICATE KEY UPDATE creatureAdds = creatureAdds + 1";
        }
        else if($action === 'creatureRemove') {
            $query = "INSERT INTO ".$this->metrics_table_name."(weekId, creatureRemoves) 
            SELECT :weekId, 1
            ON DUPLICATE KEY UPDATE creatureRemoves = creatureRemoves + 1";
        }
        else {return false;}
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":weekId", $this->weekId);
        if($stmt->execute()) {return true;}
        return false;
    }

    private function logIp() {
        $query = "SELECT * FROM ".$this->ips_table_name." WHERE ip = :ip AND weekId = :weekId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ip", $this->ip);
        $stmt->bindParam(":weekId", $this->weekId);

        if(!$stmt->execute()) {return false;}
        if(!($stmt->rowCount()>0)) {
            $query = "INSERT INTO ".$this->ips_table_name."(weekId, ip) 
            SELECT :weekId, :ip
            ON DUPLICATE KEY UPDATE ip = ip";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":weekId", $this->weekId);
            $stmt->bindParam(":ip", $this->ip);
            $stmt->execute();

            $query = "INSERT INTO ".$this->metrics_table_name."(weekId, uniques) 
            SELECT :weekId, 1
            ON DUPLICATE KEY UPDATE uniques = uniques + 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":weekId", $this->weekId);
            $stmt->execute();
        }
        return true;
    }

    function logClick() { return ($this->logIp() && $this->logMetric("click")); }
    function logCurl() { return ($this->logIp() && $this->logMetric("curl")); }
    function logRemove() { return ($this->logIp() && $this->logMetric("creatureRemove")); }

    function logAdd($code) {
        if($this->logIp() && $this->logMetric("creatureAdd")) {
            $query = "INSERT INTO ".$this->creatures_table_name."(weekId, code) 
            SELECT :weekId, :code
            ON DUPLICATE KEY UPDATE code = code";

            $stmt = $this->conn->prepare($query);

            $code=htmlspecialchars(strip_tags($code));

            $stmt->bindParam(":weekId", $this->weekId);
            $stmt->bindParam(":code", $code);

            $stmt->execute();

            return true;
        };
        return false;
    }
    
    function logLab($labname) {
        $query = "INSERT INTO ".$this->labs_table_name."(weekId, labname) 
        SELECT :weekId, :labname
        ON DUPLICATE KEY UPDATE labname = labname";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":weekId", $this->weekId);
        $stmt->bindParam(":labname", $labname);

        return ($stmt->execute());
    }

}


?>