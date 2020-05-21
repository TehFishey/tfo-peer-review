<?php
class UserClick {
  
    // database connection and table name
    private $conn;
    private $table_name = "userclicks";
  
    // object properties
    public $uip;            // string - IP address of clicking/connecting user
    public $code;           // string - 5 character creature code (ex. "6bMDs")
    public $clicked;        // string - 10 character Unix timestamp for when click occured
  
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . "(ip, code, clicked) 
            select :ip, :code, :clicked 
            on duplicate key update ip = values(ip)";
        $stmt = $this->conn->prepare($query);

        $this->uip=htmlspecialchars(strip_tags($this->uip));
        $this->code=htmlspecialchars(strip_tags($this->code));
        $this->clicked=htmlspecialchars(strip_tags($this->clicked));
        $stmt->bindParam(":ip", $this->uip);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":clicked", $this->clicked);

        echo($this->uip." Code: ".$this->code." Clicked: ".$this->clicked);

        if($stmt->execute()){
            return true;
        }
        return false; 
    }
}
?>