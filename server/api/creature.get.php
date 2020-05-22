<?php
header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// If preflight, return only the headers and not the content
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

// Include database and object files
include_once './db/db.php';
include_once './library/creature.php';
  
// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"));

// Validate incoming creature code
if(!empty($data->code) && strlen($data->code)==5) {
    $code = $data->code;
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to get creature. Creature code is invalid.")));
}

// If data validation checks are passed, encode the db key...
$creature = new Creature($db);
$creature->code = $code;

// ... and fetch other props from the db.

$creature->readOne();
$output=array();
$output["found"] = '';
$output["creature"] = array();

// If the creature does not exist in db, return false
if($creature->name==null||
    $creature->imgsrc==null||
    $creature->gotten==null||
    $creature->growthLevel==null){

    $output["found"] = 'false';
    http_response_code(200);
    echo json_encode($output);
} 

// Otherwise, return true and the object
else {
    $creature = array(
        "code" => $creature->code,
        "imgsrc" => $creature->imgsrc,
        "gotten" => $creature->gotten,
        "name" => $creature->name,
        "growthLevel" => $creature->growthLevel
    );
    
    $output["found"] = 'true';
    $output["creature"] = $creature;
    http_response_code(200);
    echo json_encode($output);
}