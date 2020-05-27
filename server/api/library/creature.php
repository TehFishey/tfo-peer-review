<?php
class Creature {
    /*-----------------------------------
    Database object for 'creatureData' (aka. 'main') table

    The 'creatureData' table contains all creature objects fetched/imported into the site from TFO. These are the creatures which are displayed by the frontend
    to users for clicking. Creatures can be add, removed, checked, and updated by cron scripts or api commands.
    --------------------------------------*/

    // database connection and table name
    private $conn;
    private $creature_table_name = "Creatures";
    private $cache_table_name = "SessionCache";
    private $click_table_name = "Clicks";
    

    // object properties
    public $session;        //
    
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