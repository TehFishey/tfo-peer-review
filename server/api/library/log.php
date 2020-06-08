<?php
/************************************************************************************** 
 * Main Data Object for Log_Metrics table.
 * 
 **************************************************************************************/
class Log {

    // database connection and table name
    private $conn;
    private $metrics_table_name = "Log_Metrics";
    private $creatures_table_name = "Log_Creatures";
    private $labs_table_name = "Log_Labs";
  
    // object properties
    public $weekId;                     // string - 7 character week id (form: year-week, ex. 2020-12)
    public $uniques;                    // integer - count of how many unique visitors site has had
    public $clicks;                     // integer - count of how many clicks site visitors have made
    public $curls;                      // integer - count of how many lab imports site visitors have made
    public $creatureAdds;               // integer - count of how many creatures site visitors have added to db
    public $creatureRemoves;            // integer - count of how many creatures site visitors have removed from db
    public $uniqueCreatures;            // (no corresponding db column) count of unique creature codes from current set 
    public $uniqueLabs;                 // (no corresponding db column) count of unique lab names from current set 
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function readCompiledLogs() {
        // Zero out props for sanitization purposes
        $this->uniques = 0;
        $this->clicks = 0;
        $this->curls = 0;
        $this->creatureAdds = 0;
        $this->creatureRemoves = 0;
        $this->creatureAdds = 0;
        $this->creatureRemoves = 0;
        $this->uniqueCreatures = 0;
        $this->uniqueLabs = 0;
        
        // Sum all values for all weeks in the metrics table, and set data object values accordingly
        $query = "SELECT 
            SUM(clicks) clicks, SUM(curls) curls, SUM(creatureAdds) creatureAdds, SUM(creatureRemoves) creatureRemoves 
            FROM ".$this->metrics_table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if(true) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            //$this->uniques = $row['uniques'];     Adding uniques together makes no practical sense...
            if($row['clicks']) $this->clicks = $row['clicks'];
            if($row['curls'])$this->curls = $row['curls'];
            if($row['creatureAdds'])$this->creatureAdds = $row['creatureAdds'];
            if($row['creatureRemoves'])$this->creatureRemoves = $row['creatureRemoves'];
        }
        
        // Count all unique creature codes in the creatures table, and set data object value.
        $query = "SELECT COUNT(DISTINCT code) FROM ".$this->creatures_table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $this->uniqueCreatures = $stmt->fetchColumn();


        // Count all unique lab names in the labs table, and set data object value.
        $query = "SELECT COUNT(DISTINCT labname) FROM ".$this->labs_table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $this->uniqueLabs = $stmt->fetchColumn();     
    }

    function readWeeklyLogs() {
        // Zero out props for sanitization purposes
        $this->uniques = 0;
        $this->clicks = 0;
        $this->curls = 0;
        $this->creatureAdds = 0;
        $this->creatureRemoves = 0;
        $this->creatureAdds = 0;
        $this->creatureRemoves = 0;
        $this->uniqueCreatures = 0;
        $this->uniqueLabs = 0;

        // Select metrics table row for current week, and set data object values.
        $query = "SELECT * FROM ".$this->metrics_table_name." WHERE weekId = :weekId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":weekId", $this->weekId);
        $stmt->execute();

        if($stmt->rowCount()>0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->uniques = $row['uniques'];
            $this->clicks = $row['clicks'];
            $this->curls = $row['curls'];
            $this->creatureAdds = $row['creatureAdds'];
            $this->creatureRemoves = $row['creatureRemoves'];
        }

        // Count all creatures table entries for this week, and set data object value.
        $query = "SELECT COUNT(*) FROM ".$this->creatures_table_name." 
            WHERE weekId = :weekId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":weekId", $this->weekId);
        $stmt->execute();

        $this->uniqueCreatures = $stmt->fetchColumn();

        // Count all labs table entries for this week, and set data object value.
        $query = "SELECT COUNT(*) FROM ".$this->labs_table_name." 
            WHERE weekId = :weekId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":weekId", $this->weekId);
        $stmt->execute();

        $this->uniqueLabs = $stmt->fetchColumn();
    }
}
?>