<?php
class CreatureKey {
  
    // database connection and table name
    private $conn;
    private $table_name = "markedkeys";
  
    // object properties
    public $code;           // string - 5 character creature code (ex. "6bMDs")
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function read() {
        $query = "SELECT k.code FROM " . $this->table_name . " AS k";
      
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
      
        return $stmt;
    }

    function clear() {
        $query = "TRUNCATE TABLE " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        if($stmt->execute()){ return true; }
        return false;
    }
}
?>