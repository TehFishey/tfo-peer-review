<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
// include database and object files
include_once '../config/database.php';
include_once '../objects/creature.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection();
  
// prepare object
$creature = new Creature($db);
  
// get id of object to be edited
$data = json_decode(file_get_contents("php://input"));
  
// set ID property of creature to be edited
$creature->id = $data->id;
  
// set object property values
$creature->code = $data->code;
$creature->imgsrc = $data->imgsrc;
$creature->gotten = $data->gotten;
$creature->name = $data->name;
$creature->growth = $data->growth;
  
// update the object
if($creature->update()) {
  
    // set response code - 200 ok
    http_response_code(200);
  
    // tell the user
    echo json_encode(array("message" => "(200) Creature was updated."));
}
  
// if unable to update the object, tell the user
else {
  
    // set response code - 503 service unavailable
    http_response_code(503);
  
    // tell the user
    echo json_encode(array("message" => "(503) Unable to update creature."));
}
?>