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
  
// get database connection
$database = new Database();
$db = $database->getConnection();
  
// prepare object
$creature = new creature($db);
  
// set code property of record to read
$creature->code = isset($_GET['code']) ? $_GET['code'] : die();
  
// read the details of object to be edited
$creature->readOne();
  
if($creature->name!=null){
    // create array
    $creature_arr = array(
        "code" => $creature->code,
        "imgsrc" => $creature->imgsrc,
        "gotten" => $creature->gotten,
        "name" => $creature->name,
        "growthLevel" => $creature->growthLevel
  
    );
  
    // set response code - 200 OK
    http_response_code(200);
  
    // make it json format
    echo json_encode($creature_arr);
} else {
    // tell the user object does not exist
    echo json_encode(array("error" => "(404) Creature does not exist."));
}
?>