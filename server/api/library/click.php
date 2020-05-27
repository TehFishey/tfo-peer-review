<?php
class Click {
    /*-----------------------------------
    Database object for 'uuidClicks' table

    The 'uuidClicks' table logs interaction events (either creature clicks or creature flaggings) between users ($uuid) and
    creatures stored in the main db table ($code). uuidClick objects are given a timestamp ($time) when logged. 

    This db table is read when serving users creatures to click on, to prevent duplicates from being served over a 24 hour 
    timespan. After 1 day, entries are removed from this table by cron.
    --------------------------------------*/

    // database connection and table name
    private $conn;
    private $table_name = "tbl_uuidClicks";
  
    // object properties
    public $uuid;           // string - 36 character uuid of clicking/connecting user (from browser cookie)
    public $code;           // string - 5 character creature code (ex. "6bMDs")
    public $time;           // string - 10 character Unix timestamp for when click occured
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . "(uuid, code, time) 
            select :uuid, :code, :time 
            on duplicate key update uuid = values(uuid)";
        $stmt = $this->conn->prepare($query);

        $this->uuid=htmlspecialchars(strip_tags($this->uuid));
        $this->code=htmlspecialchars(strip_tags($this->code));
        $this->time=htmlspecialchars(strip_tags($this->time));
        $stmt->bindParam(":uuid", $this->uuid);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":time", $this->time);

        //echo($this->uuid." Code: ".$this->code." Clicked: ".$this->time);

        if($stmt->execute()){ return true; }
        return false; 
    }
}
?>