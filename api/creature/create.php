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
  
$database = new Database();
$db = $database->getConnection();
  
$creature = new Creature($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));
  
// make sure data is not empty
if (
    !empty($data->code) &&
    !empty($data->imgsrc) &&
    !empty($data->gotten) &&
    !empty($data->name) &&
    !empty($data->growth)
) {
  
    // set property values
    $creature->code = $data->code;
    $creature->imgsrc = $data->imgsrc;
    $creature->gotten = $data->gotten;
    $creature->name = $data->name;
    $creature->growth = $data->growth;
  
    // create the object
    if($creature->create()){
  
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "(201) Creature was created."));
    }
  
    // if unable to create the object, tell the user
    else {
  
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "(503) Unable to create creature."));
    }
}
  
// tell the user data is incomplete
else{
  
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "(400) Unable to create creature. Data is incomplete."));
}
?>