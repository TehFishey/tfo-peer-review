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
  
// make sure data is not empty
if(
    !empty($data->code) &&
    !empty($data->imgsrc) &&
    !empty($data->gotten) &&
    !empty($data->name) &&
    !empty($data->growthLevel)
){
  
    // set property values
    $creature->code = $data->code;
    $creature->imgsrc = $data->imgsrc;
    $creature->gotten = $data->gotten;
    $creature->name = $data->name;
    $creature->growthLevel = $data->growthLevel;
  
    // create the object
    if($creature->replace()){
        echo json_encode(array("message" => "(201) Creature was created."));
    } else {
        echo json_encode(array("error" => "(503) Unable to create creature."));
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