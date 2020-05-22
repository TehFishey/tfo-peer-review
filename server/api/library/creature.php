<?php
class Creature {
  
    // database connection and table name
    private $conn;
    private $table_name = "creatures";
    private $uc_table_name = "userclicks";
  
    // object properties
    public $code;           // string - 5 character creature code (ex. "6bMDs")
    public $imgsrc;         // string - 60 character image src (ex. "https:\/\/finaloutpost.net\/s\/6bMDs.png")
    public $gotten;         // string - 10 character Unix timestamp for when creature was aquired
    public $name;           // string - 30 creature name (or Unnamed) (ex. "Unnamed")
    public $growthLevel;    // string - 1 character creature growthLevel level (1-egg, 2-hatch, 3-mature) (ex. "1")
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function readSet($uuid, $count) {
        // Retrieves $count random 'creatures' entries where no 'userclicks' entry match the creature code
        $query = "SELECT c.code, c.imgsrc, c.gotten, c.name, c.growthLevel 
            FROM " . $this->table_name . " AS c LEFT OUTER JOIN " . $this->uc_table_name . " AS uc ON c.code = uc.code 
            GROUP BY c.code 
            HAVING COUNT(CASE WHEN uc.uuid = '" . $uuid . "' THEN 1 END) = 0
            ORDER BY RAND()
            LIMIT ".$count;

        $stmt = $this->conn->prepare($query);
        //echo("UUID: ".$uuid." Count: ".$count);

        // execute query
        $stmt->execute();
      
        return $stmt;
    }

    function readOne() {
        // query to read single record
        $query = "SELECT c.code, c.imgsrc, c.gotten, c.name, c.growthLevel FROM "
                    . $this->table_name . " AS c WHERE c.code = ? LIMIT 0,1";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );
      
        // bind code of product to be updated
        $stmt->bindParam(1, $this->code);
      
        // execute query
        $stmt->execute();
      
        // get retrieved row
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // set values to object properties
            //$this->code = $row['code'];
            $this->imgsrc = $row['imgsrc'];
            $this->gotten = $row['gotten'];
            $this->name = $row['name'];
            $this->growthLevel = $row['growthLevel'];
        }
    }

    function replace() {
        // For use instead of create() and update(). 
        // 'code' is a primary key, so this should work fine.

        $query = "REPLACE INTO " . $this->table_name . " SET 
            code=:code, imgsrc=:imgsrc, gotten=:gotten, name=:name, growthLevel=:growthLevel";
      
        // prepare query
        $stmt = $this->conn->prepare($query);
      
        // sanitize
        $this->imgsrc=htmlspecialchars(strip_tags($this->imgsrc));
        $this->gotten=htmlspecialchars(strip_tags($this->gotten));
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->growthLevel=htmlspecialchars(strip_tags($this->growthLevel));
      
        // bind values
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":imgsrc", $this->imgsrc);
        $stmt->bindParam(":gotten", $this->gotten);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":growthLevel", $this->growthLevel);
      
        // execute query
        if($stmt->execute()){
            return true;
        }
      
        return false;  
    }

    function delete() {
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE code = ?";
  
        // prepare query
        $stmt = $this->conn->prepare($query);
  
        // sanitize
        $this->code=htmlspecialchars(strip_tags($this->code));
  
        // bind code of record to delete
        $stmt->bindParam(1, $this->code);
  
        // execute query
        if($stmt->execute()){
            return true;
        }
  
        return false;
    }
}


?>