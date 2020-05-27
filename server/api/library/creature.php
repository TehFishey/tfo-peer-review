<?php
/************************************************************************************** 
 * Main Data Object for "Creatures" and "SessionCache" tables.
 * "Creatures" Primary Key: code
 * "SessionCache" Primary Key: composite(session, code)
 * 
 * Description:
 * The "Creatures" table is the main table of this site; it tracks all data from creatures that users 
 * have imported to the site from TFO. Entries from the Creatures table are read and served to the frontend for
 * user interaction; they can also be checked, imported, and deleted in response to frontend input.
 * 
 * It is important to note that "Creatures" entries are never passed to this table directly. Creature objects are
 * fetched from TLO via cURL in response to user input; the objects are stored in the "SessionCache" table, until
 * such time as a user decides to add any number of them to "Creatures". Users can also delete entries from "Creatures",
 * but only if they have stored a corresponding entry in "SessionCache".
 * 
 * This object is used to interact with both the "Creatures" and "SessionCache" tables, via different methods. The 
 * main difference between these two is that "SessionCache" entries require a "session" property; this is a foreign
 * key which maps them to the "Sessions" table.
 * 
 * Methods:
 * ->readSet($uuid, $count) - Reads $count entries from "Creatures", which have not yet been interacted with by
 *                            $uuid (determined by checking codes against "Clicks" table.) This method is used 
 *                            in the creature/get endpoint to serve creatures for frontend display.
 *                               Requires: $uuid, $count
 * 
 * ->readOne() - Searches for a "Creature" entry matching $this->code. If found, maps that entry's fields into 
 *               the data object's props and returns True; otherwise, returns False. This method is used by
 *               multiple endpoints for validation and other purposes, but is never invoked directly by the user.
 *                  Requires: $this->code 
 * 
 * ->replace() - Used as a catch-all for Insert and Update queries for the "Creatures" table. Creates/updates 
 *               a "Creatures" entry with fields set by the data object's props. Currently deprecated and unused.
 *                  Requires: $this->code, $this->imgsrc, $this->gotten, $this->name, $this->growthLevel, $this->isStunted
 * 
 * ->update() - Highly specific update command; ONLY updates the "growthLevel" and and "isStunted" fields of a "Creatures"
 *              table entry matching $this->code. Field values are taken from the object's corresponding props. This method
 *              is only used by the update-db cron script - its limitations are dictated by the outputs of the TLO API's 
 *              'multipleEntries' command.
 *                  Requires: $this->code, $this->growthLevel, $this->isStunted
 * 
 * ->delete() - Deletes a "Creatures" table entry matching $this->code. This method is used by the creature/delete endpoint
 *              and the update-db cron script.
 *                  Requires: $this->code 
 * 
 * ->replaceInCache() - As the replace() method, but for the "CreatureCache" table instead. Used by the creature/fetch endpoint
 *                      to add creatures retrieved by TLO to "CreatureCache". 
 *                          Requires: $this->session $this->code, $this->imgsrc, $this->gotten, $this->name, $this->growthLevel, $this->isStunted
 * 
 * ->importFromCache() - Copies an entry from "CreatureCache" to "Creatures". "CreatureCache" entry must match $this->code AND $this->session 
 *                       in order to be copied. This method is called by the creature/create endpoint, after a user has chosen to 'add' certain
 *                       creatures to the site.
 *                          Requires: $this->session, $this->code
 * 
 * ->readCachedCodes() - Returns a list of all 'code' values stored in "CreatureCache". This method is used by multiple endpoints to validate
 *                       user inputs.
 *                          Requires: Nothing
 * 
 **************************************************************************************/

class Creature {
    // database connection and table names
    private $conn;
    private $creature_table_name = "Creatures";
    private $cache_table_name = "SessionCache";
    private $click_table_name = "Clicks";
    

    // object properties
    public $session;        // int - integer representing unique session id in Sessions table. ONLY USED IN SessionCache OPERATIONS!
    
    public $code;           // string - 5 character creature code (ex. "6bMDs")
    public $imgsrc;         // string - 60 character image src (ex. "https:\/\/finaloutpost.net\/s\/6bMDs.png")
    public $gotten;         // string - 10 character Unix timestamp for when creature was aquired
    public $name;           // string - 30 creature name (or Unnamed) (ex. "Unnamed")
    public $growthLevel;    // string - 1 character creature growthLevel level (1-egg, 2-hatch, 3-mature) (ex. "1")
    public $isStunted;      // string - 5 character boolean (true/false) for if the creature is a stunted capsule/child
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }


    function readSet($uuid, $count) {
        // Retrieves $count random 'creatures' entries where no 'userclicks' entry match the creature code
        $query = "SELECT c.code, c.imgsrc, c.gotten, c.name, c.growthLevel 
            FROM " . $this->creature_table_name . " AS c LEFT OUTER JOIN " . $this->click_table_name . " AS uc ON c.code = uc.code 
            GROUP BY c.code 
            HAVING COUNT(CASE WHEN uc.uuid=:uuid THEN 1 END) = 0
            ORDER BY RAND()
            LIMIT :count";
        $stmt = $this->conn->prepare($query);
        //echo("UUID: ".$uuid." Count: ".$count);

        // sanitize
        $uuid=htmlspecialchars(strip_tags($uuid));
        $count=htmlspecialchars(strip_tags($count));

        // bind values
        $stmt->bindParam(":uuid", $uuid);
        $stmt->bindParam(":count", $count);

        // execute query
        $stmt->execute();
      
        return $stmt;
    }

    function readOne() {
        // query to read single record
        $query = "SELECT c.code, c.imgsrc, c.gotten, c.name, c.growthLevel, c.isStunted FROM "
                    . $this->creature_table_name . " AS c WHERE c.code = ? LIMIT 0,1";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );
      
        // bind code of product to be updated
        $stmt->bindParam(1, $this->code);
      
        // execute query
        $stmt->execute();
      
        // get retrieved row
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // set values to object properties
            $this->imgsrc = $row['imgsrc'];
            $this->gotten = $row['gotten'];
            $this->name = $row['name'];
            $this->growthLevel = $row['growthLevel'];
            $this->isStunted = $row['isStunted'];
            return true;
        }
        return false;
    }

    function replace() {
        // For use instead of create() and update(). 
        // 'code' is a primary key, so this should work fine.

        $query = "REPLACE INTO " . $this->creature_table_name . " SET 
            code=:code, imgsrc=:imgsrc, gotten=:gotten, name=:name, growthLevel=:growthLevel, isStunted=:isStunted";
      
        // prepare query
        $stmt = $this->conn->prepare($query);
      
        // sanitize
        $this->code=htmlspecialchars(strip_tags($this->code));
        $this->imgsrc=htmlspecialchars(strip_tags($this->imgsrc));
        $this->gotten=htmlspecialchars(strip_tags($this->gotten));
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->growthLevel=htmlspecialchars(strip_tags($this->growthLevel));
        $this->isStunted=htmlspecialchars(strip_tags($this->isStunted));
      
        // bind values
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":imgsrc", $this->imgsrc);
        $stmt->bindParam(":gotten", $this->gotten);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":growthLevel", $this->growthLevel);
        $stmt->bindParam(":isStunted", $this->isStunted);
      
        // execute query
        if($stmt->execute()){ return true; }
        return false;  
    }

    function update() {
        // Dumb update command
        $query = "UPDATE ".$this->creature_table_name." 
            SET growthLevel=:growthLevel, isStunted=:isStunted WHERE code=:code";
        $stmt = $this->conn->prepare($query);
      
        // sanitize
        $this->code=htmlspecialchars(strip_tags($this->code));
        $this->growthLevel=htmlspecialchars(strip_tags($this->growthLevel));
        $this->isStunted=htmlspecialchars(strip_tags($this->isStunted));
      
        // bind values
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":growthLevel", $this->growthLevel);
        $stmt->bindParam(":isStunted", $this->isStunted);
      
        // execute query
        if($stmt->execute()){ return true; }
        return false;  
    }

    function delete() {
        // delete query
        $query = "DELETE FROM " . $this->creature_table_name . " WHERE code=:code";
        $stmt = $this->conn->prepare($query);
  
        // sanitize
        $this->code=htmlspecialchars(strip_tags($this->code));

        // bind values
        $stmt->bindParam(":code", $this->code);
  
        // execute query
        if($stmt->execute()){ return true; }
        return false;
    }

    function replaceInCache() {
        // For use instead of create() and update(). 
        // 'code' is a primary key, so this should work fine.

        $query = "REPLACE INTO " . $this->cache_table_name . " SET 
            sessionId=:session, code=:code, imgsrc=:imgsrc, gotten=:gotten, name=:name, growthLevel=:growthLevel, isStunted=:isStunted";
      
        // prepare query
        $stmt = $this->conn->prepare($query);
      
        // sanitize
        $this->session=htmlspecialchars(strip_tags($this->session));
        $this->code=htmlspecialchars(strip_tags($this->code));
        $this->imgsrc=htmlspecialchars(strip_tags($this->imgsrc));
        $this->gotten=htmlspecialchars(strip_tags($this->gotten));
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->growthLevel=htmlspecialchars(strip_tags($this->growthLevel));
        $this->isStunted=htmlspecialchars(strip_tags($this->isStunted));
      
        // bind values
        $stmt->bindParam(":session", $this->session);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":imgsrc", $this->imgsrc);
        $stmt->bindParam(":gotten", $this->gotten);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":growthLevel", $this->growthLevel);
        $stmt->bindParam(":isStunted", $this->isStunted);
      
        // execute query
        if($stmt->execute()){ return true; }
        return false;  
    }

    function importFromCache() {

        $query = "INSERT INTO ".$this->creature_table_name."(code, imgsrc, gotten, name, growthLevel, isStunted) 
            SELECT cc.code, cc.imgsrc, cc.gotten, cc.name, cc.growthLevel, cc.isStunted FROM ".$this->cache_table_name." AS cc 
            WHERE (cc.sessionId=:session AND cc.code=:code) 
            ON DUPLICATE KEY UPDATE imgsrc=cc.imgsrc, gotten=cc.gotten, name=cc.name, growthLevel=cc.growthLevel, isStunted=cc.isStunted";
        $stmt = $this->conn->prepare($query);

        $this->session=htmlspecialchars(strip_tags($this->session));
        $this->code=htmlspecialchars(strip_tags($this->code));
        $stmt->bindParam(":session", $this->session);
        $stmt->bindParam(":code", $this->code);

        if($stmt->execute()){ return true; }
        return false; 
    }

    function readCachedCodes() {
        $query = "SELECT code FROM ".$this->cache_table_name.
            " GROUP BY code";

        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();
      
        return $stmt;
    }
}


?>