<?php

class Log {

    // database connection and table name
    private $conn;
    private $short_log_table_name = "Log_Weekly";
    private $long_log_table_name = "Log_Compiled";
  
    // object properties
    public $weekId;                      // string - 7 character week id (form: year-week, ex. 2020-12)
    public $pageViews;                  // integer - count of how many page views site has had
    public $uniques;                    // integer - count of how many unique visitors site has had
    public $clicks;                     // integer - count of how many clicks site visitors have made
    public $curls;                      // integer - count of how many lab imports site visitors have made
    public $creatureAdds;               // integer - count of how many creatures site visitors have added to db
    public $creatureRemoves;            // integer - count of how many creatures site visitors have removed from db
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function readCompiledLogs() {
        $query = "SELECT SUM(clicks) clicks, SUM(curls) curls, SUM(creatureAdds) creatureAdds, SUM(creatureRemoves) creatureRemoves 
            FROM ".$this->long_log_table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //$this->uniques = $row['uniques'];     Adding uniques together makes no practical sense...
        $this->clicks = $row['clicks'];
        $this->curls = $row['curls'];
        $this->creatureAdds = $row['creatureAdds'];
        $this->creatureRemoves = $row['creatureRemoves'];
    }

    function readWeeklyLogs() {
        $query = "SELECT COUNT(DISTINCT ip) FROM ".$this->short_log_table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $this->uniques = $stmt->fetchColumn();

        $query = "SELECT * FROM ".$this->short_log_table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if($stmt->rowCount()>0) { 
            $this->clicks = 0;
            $this->curls = 0;
            $this->creatureAdds = 0;
            $this->creatureRemoves = 0;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $action = $row['action'];
        
                if($action=='click') {$this->clicks += $row['count'];} 
                else if($action=='curl') {$this->curls += $row['count'];} 
                else if($action=='creatureAdd') {$this->creatureAdds += $row['count'];} 
                else if($action=='creatureRemove') {$this->creatureRemoves += $row['count'];}
            }
        }
    }
}
?>