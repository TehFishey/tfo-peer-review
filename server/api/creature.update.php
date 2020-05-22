<?php
header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// If preflight, return only the headers and not the content
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

// Include database and object file
include_once './db/db.php';
include_once './library/creature.php';
include_once './library/curl.tfo.php';

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
// get posted data

// Validate incoming creature object's form
if(!empty($data->code) && strlen($data->code)==5 &&
    !empty($data->imgsrc) &&
    !empty($data->gotten) &&
    !empty($data->name) &&
    !empty($data->growthLevel)){

    /// Update/Validate creature data before adding to db    
    $curl = new TFO_cURL;
    $curl->action = 'creature';
    $curl->var = $data->code;
    $curl->execute();

    $curl_data = json_decode($curl->output);
    if(!empty($curl->error) || $curl_data->error) {
        http_response_code(404);
        die(json_encode(array("message" => "(404) Unable to find creature on TLO.")));
    }
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to create creature. Data is incomplete or invalid.")));
}

// If data validation checks are passed, encode the new creature data...
$creature = new Creature($db);

$creature->code = $curl_data->{0}->code;
$creature->imgsrc = $curl_data->{0}->imgsrc;
$creature->gotten = $curl_data->{0}->gotten;
$creature->name = $curl_data->{0}->name;
$creature->growthLevel = $curl_data->{0}->growthLevel;
  
// ...and create the object.
if($creature->replace()){
    http_response_code(201);
    echo json_encode(array("message" => "(201) Creature was created."));
} else {
    http_response_code(503);
    echo json_encode(array("error" => "(503) Unable to create creature."));
}