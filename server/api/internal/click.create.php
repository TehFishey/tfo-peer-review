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
include_once './library/click.php';
include_once './library/creature.php';
  
$database = new Database();
$db = $database->getConnection();
  
$click = new UserClick($db);
$creature = new creature($db);

$data = json_decode(file_get_contents("php://input"));
if(!empty($data->code)){
    
    // first check if creature code exists in creature db (input validation)
    $creature->code = $data->code;
    $creature->readOne();
    if($creature->name!=null){

        // set property values
        $click->uip = $_SERVER['REMOTE_ADDR'];
        $click->code = $data->code;
        $click->clicked = (string) time();
  
        // create the object
        if($click->create()){
            echo json_encode(array("message" => "(201) entry was created."));
        } else {
            echo json_encode(array("error" => "(503) Unable to create entry."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "(400) Unable to create entry. Creature code is invalid."));
    }
}
else{
    http_response_code(400);
    echo json_encode(array("message" => "(400) Unable to create entry. Data is incomplete."));
}
?>