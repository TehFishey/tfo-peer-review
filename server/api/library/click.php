<?php
class UserClick {
  
    // database connection and table name
    private $conn;
    private $table_name = "userclicks";
  
    // object properties
    public $uuid;           // string - 36 character uuid of clicking/connecting user (from browser cookie)
    public $code;           // string - 5 character creature code (ex. "6bMDs")
    public $clicked;        // string - 10 character Unix timestamp for when click occured
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . "(uuid, code, clicked) 
            select :uuid, :code, :clicked 
            on duplicate key update uuid = values(uuid)";
        $stmt = $this->conn->prepare($query);

        $this->uuid=htmlspecialchars(strip_tags($this->uuid));
        $this->code=htmlspecialchars(strip_tags($this->code));
        $this->clicked=htmlspecialchars(strip_tags($this->clicked));
        $stmt->bindParam(":uuid", $this->uuid);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":clicked", $this->clicked);

        //echo($this->uuid." Code: ".$this->code." Clicked: ".$this->clicked);

        if($stmt->execute()){
            return true;
        }
        return false; 
    }
}
?>