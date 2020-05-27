<?php
class Flag {
    /*-----------------------------------
    Database object for 'uuidInvalidFlags' table

    The 'uuidInvalidFlags' table tracks creatures ($code) which have been 'flagged' by users as being invalid for inclusion in the main db table,
    likely because the creature has grown up. For forward-compatability purposes, 'uuidInvalidFlags' also tracks what user(s) ($uuid) flagged the creatures.

    This db table is read and cleared every 10 minutes by cron; marked creature entries are re-fetched for the main table from TFO, 
    and all invalid entries in the main table are then purged.
    --------------------------------------*/

    // database connection and table name
    private $conn;
    private $table_name = "FlaggedCodes";
  
    // object properties
    public $uuid;           // string - 36 character uuid of clicking/connecting user (from browser cookie)
    public $code;           // string - 5 character creature code (ex. "6bMDs")
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // create -> Add creature code:uuid pair to table if it doesn't already exist. 
    function create() {
        $query = "INSERT INTO " . $this->table_name . "(uuid, code) 
            select :uuid, :code 
            on duplicate key update uuid = values(uuid)";
        $stmt = $this->conn->prepare($query);
        
        $this->uuid=htmlspecialchars(strip_tags($this->uuid));
        $this->code=htmlspecialchars(strip_tags($this->code));
        $stmt->bindParam(":uuid", $this->uuid);
        $stmt->bindParam(":code", $this->code);

        if($stmt->execute()){ return true; }
        return false;  
    }

    // readCodes -> Read all creature codes in the 'FlaggedCodes' table.
    function readCodes() {
        $query = "SELECT code FROM ".$this->table_name. 
            "GROUP BY code";
        $stmt = $this->conn->prepare($query);

        if($stmt->execute()){ return $stmt; }
        return false;  
    }

    // clear -> clear the 'FlaggedCodes' table.
    function clear() {
        $query = "TRUNCATE TABLE " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        if($stmt->execute()){ return true; }
        return false;
    }
}
?>