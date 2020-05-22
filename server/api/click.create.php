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
include_once './library/click.php';
include_once './library/creature.php';

// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"));

// Validate uuid (stored in tfopr-uuid browser cookie)
if($_COOKIE['tfopr-uuid']!=null && strlen($_COOKIE['tfopr-uuid'])==36) {
    $uuid = $_COOKIE['tfopr-uuid'];
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to create userclick. UUID token is invalid.")));
}

// Validate incoming creature code
if(!empty($data->code) && strlen($data->code)==5) {
    $code = $data->code;

    // Check if creature exists in server db
    // (if it doesn't, how could someone click it? This should never happen.)
    $creature = new creature($db);
    $creature->code = $code;
    $creature->readOne();
    if($creature->name==null){
        http_response_code(409);
        die(json_encode(array("message" => "(409) Creature code does not exist in database.")));
    }
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to create userclick. Creature code is invalid.")));
}

// If data validation checks are passed, encode the db object...
$click = new UserClick($db);

$click->uuid = $uuid;
$click->code = $code;
$click->clicked = (string) time();

// ... and add it to the db.
if($click->create()){
    http_response_code(201);
    echo json_encode(array("message" => "(201) entry was created."));
} else {
    http_response_code(503);
    die(json_encode(array("error" => "(503) Unable to create entry.")));
}