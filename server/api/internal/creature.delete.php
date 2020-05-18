<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// if preflight, return only the headers and not the content
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

// include database and object file
include_once './db/db.php';
include_once './library/creature.php';
  
$database = new Database();
$db = $database->getConnection();
  
$creature = new Creature($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));
  
// set creature code to be deleted
$creature->code = $data->code;
  
// delete the object
if($creature->delete()) {
  
    // set response code - 200 ok
    http_response_code(200);
  
    // tell the user
    echo json_encode(array("message" => "(200) creature was deleted."));
} else {
    echo json_encode(array("error" => "(503) Unable to delete creature."));
}
?>