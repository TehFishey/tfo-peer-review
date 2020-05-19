<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// if preflight, return only the headers and not the content
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

// include database and object files
include_once './db/db.php';
include_once './library/creature.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

// get ip address of frontend
$uip = $_SERVER['REMOTE_ADDR'];

// initialize object
$creature = new Creature($db);
  
// query for group; pass in user ip
$stmt = $creature->readSet($uip);
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0) {
  
    // products array
    $creatures_arr=array();
    $creatures_arr["records"]=array();
  
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $creature_item=array(
            "code" => $code,
            "imgsrc" => $imgsrc,
            "gotten" => $gotten,
            "name" => $name,
            "growthLevel" => $growthLevel
        );
  
        array_push($creatures_arr["records"], $creature_item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show object data in json format
    echo json_encode($creatures_arr);
} else {
    // tell the user no objects found
    echo json_encode(
        array("error" => "(404) No creatures found.")
    );
}