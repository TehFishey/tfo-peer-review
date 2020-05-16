<?php
class Creature {
  
    // database connection and table name
    private $conn;
    private $table_name = "creatures";
  
    // object properties
    public $code;   // string - 5 character creature code (ex. "6bMDs")
    public $imgsrc; // string - creature image src (ex. "https:\/\/finaloutpost.net\/s\/6bMDs.png")
    public $gotten; // string - db entry creation Unix timestamp (ex. 1589163235)
    public $name;   // string - creature name (or Unnamed) (ex. "Unnamed")
    public $growthLevel; // string - creature growthLevel level (1-egg, 2-hatch, 3-mature) (ex. "1")
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function read() {
        // select all query
        $query = "SELECT c.code, c.imgsrc, c.gotten, c.name, c.growthLevel FROM " 
                 . $this->table_name . " AS c";
      
        // prepare query statement
        $stmt = $this->conn->prepare($query);
      
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
    
    /*
    function create() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET 
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

    function update() {
        // update query
        $query = "UPDATE " . $this->table_name . " SET
           code=:code, imgsrc=:imgsrc, gotten=:gotten, name=:name, growthLevel=:growthLevel 
           WHERE id = :id";
  
        // prepare query statement
        $stmt = $this->conn->prepare($query);
  
        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->code=htmlspecialchars(strip_tags($this->code));
        $this->imgsrc=htmlspecialchars(strip_tags($this->imgsrc));
        $this->gotten=htmlspecialchars(strip_tags($this->gotten));
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->growthLevel=htmlspecialchars(strip_tags($this->growthLevel));
  
        // bind new values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':imgsrc', $this->imgsrc);
        $stmt->bindParam(':gotten', $this->gotten);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':growthLevel', $this->growthLevel);
  
        // execute the query
        if($stmt->execute()){
            return true;
        }
  
        return false;
    }
    */

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