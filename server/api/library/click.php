<?php
/************************************************************************************** 
 * Main Data Object for "Clicks" table.
 * Primary Key: Composite(uuid, code)
 * 
 * Description:
 * The "Clicks" table tracks interaction events (eg. when a user clicks or flags a creature). It does this
 * by storing relationships between users ($uuid) and creatures ($code) as composite keys. Entries are also
 * are given a timestamp ($time) when logged. 
 * 
 * This db table is read when serving users creatures to click on, to prevent the same creature from being
 * served twice to the same user within 1 day. After a day elapses, entries are removed from this table by cron.
 * 
 * Methods:
 * ->create() - Used to create a click entry. Called from endpoint.
 *                Requires: $this->uuid, $this->code, $this->time
 **************************************************************************************/

class Click {

    // database connection and table name
    private $conn;
    private $table_name = "Clicks";
  
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