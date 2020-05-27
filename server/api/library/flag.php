<?php
/************************************************************************************** 
 * Main Data Object for "FlaggedCodes" table.
 * Primary Key: Composite(uuid, code)
 * 
 * Description:
 * The "FlaggedCodes" table tracks entries in the "Creatures" table which have been 'flagged' by users as being
 * invalid for inclusion on the website (either because the creature is an adult, is dead, is stunted, etc.) The table
 * "Creatures" entries by their primary keys ($code). It also stores the corresponding $uuid of the user who flagged
 * each creature.
 * 
 * "FlaggedCodes" is periodically read by the 'update-db' cron event. Marked creature entries are re-fetched from
 * TFO through a specialized cURL endpoint; non-existant creatures are deleted from "Creatures", and existing ones
 * have their their "growthLevel" and "isStunted" fields updated to current values. Confirmed invalid creatures
 * are then purged from "Creatures" by the 'flush-db' cron event. After 'update-db' reads "FlaggedCodes", the table's
 * contents are cleared.
 * 
 * (Note: the uuid field of "FlaggedCodes" is not currently used for anything; it is only there in case features are 
 * implemented at a later date which might benefit from it.)
 * 
 * Methods:
 * ->create() - Used to create a "FlaggedCodes" table entry. Called from the flag/create endpoint.
 *                Requires: $this->uuid, $this->code
 * 
 * ->readCodes() - Reads out all unique creature codes in "FlaggedCodes". Called from the 'update-db' cron event.
 *                   Requires: Nothing
 * 
 * ->clear() - Truncates/clears the "FlaggedCodes" table. Called at the end of the 'update-db' cron event.
 *               Requires: Nothing
 **************************************************************************************/
class Flag {
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
        $query = "SELECT f.code FROM ".$this->table_name." AS f 
            GROUP BY f.code";
        $stmt = $this->conn->prepare($query);

        if($stmt->execute()){ return $stmt; }
        return false;  
    }

    // clear -> clear the 'FlaggedCodes' table.
    function clear() {
        $query = "TRUNCATE TABLE ".$this->table_name;

        $stmt = $this->conn->prepare($query);
        if($stmt->execute()){ return true; }
        return false;
    }
}
?>