<?php
/************************************************************************************** 
 * Main Data Object for "Log_Weekly" and "Log_Compiled" tables.
 * "Log_Weekly" Primary Key: Composite (ip, action) (Not accessible here)
 * "Log_Compiled" Primary Key: weekId
 * 
 * Description:
 * The "Log_Weekly" and "Log_Compiled" tables track rudimentary metrics related to website use, including clicks entered, 
 * curl requests attempted, unique visitor IPs, and so forth. During a weekly rollover cron, stats from the "Log_Weekly" table
 * are compiled into a single entry in the "Log_Compiled" table. User IPs are only tracked in the weekly table (associated 
 * with specific user actions).
 * 
 * (Note: the pageViews field of "Log_Weekly" is not currently used for anything; it is only there in case features are 
 * implemented at a later date which might make use of it.)
 * 
 * Methods:
 * ->readCompiledLogs() - Reads the "Log_Compiled" Table and sets object properties equal to sum values of each column.
 *                        Used to fetch data for the frontend metrics widget.
 *                          Requires: Nothing
 * 
 * ->readWeeklyLogs() - Reads the "Log_Weekly" Table and sets object properties equal to the sum counts of each type of action.
 *                      Used to fetch data for the frontend metrics widget.
 *                          Requires: Nothing
 * 
 **************************************************************************************/
class Log {

    // database connection and table name
    private $conn;
    private $short_log_table_name = "Log_Weekly";
    private $long_log_table_name = "Log_Compiled";
  
    // object properties
    public $weekId;                     // string - 7 character week id (form: year-week, ex. 2020-12)
    public $pageViews;                  // integer - count of how many page views site has had (CURRENTLY UNUSED)
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