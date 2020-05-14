<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
  
// include database and object files
include_once '../config/database.php';
include_once '../objects/creature.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection();
  
// prepare object
$creature = new creature($db);
  
// set ID property of record to read
$creature->id = isset($_GET['id']) ? $_GET['id'] : die();
  
// read the details of object to be edited
$creature->readOne();
  
if($creature->name!=null){
    // create array
    $creature_arr = array(
        "id" =>  $creature->id,
        "code" => $creature->code,
        "imgsrc" => $creature->imgsrc,
        "gotten" => $creature->gotten,
        "name" => $creature->name,
        "growth" => $creature->growth
  
    );
  
    // set response code - 200 OK
    http_response_code(200);
  
    // make it json format
    echo json_encode($creature_arr);
}
  
else{
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user object does not exist
    echo json_encode(array("message" => "creature does not exist."));
}
?>