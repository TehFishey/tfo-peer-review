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
include_once './library/ckey.php';
  
$database = new Database();
$db = $database->getConnection();
  
$ckey = new CreatureKey($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));
  
// make sure data is not empty
if(
    !empty($data->code)
){
  
    // set property values
    $ckey->code = $data->code;
  
    // create the object
    if($ckey->replace()){
        echo json_encode(array("message" => "(201) Key was marked."));
    } else {
        echo json_encode(array("error" => "(503) Unable to mark key."));
    }
}
  
// tell the user data is incomplete
else{
  
    // set response code - 400 bad request
    http_response_code(400);
  
   // tell the user
    echo json_encode(array("message" => "(400) Unable to mark key. Please provide a creature code."));
}
?>