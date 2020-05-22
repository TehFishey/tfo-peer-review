<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
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
$creature = new Creature($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(!empty($data->code)) {
    $creature->code = $data->code;
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
} else {
  
    // set response code - 400 bad request
    http_response_code(400);
  
   // tell the user
    echo json_encode(array("message" => "(400) Unable to get creature. Please provide a creature code."));
}
?>




?>