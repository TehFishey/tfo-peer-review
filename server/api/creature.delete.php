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
include_once './utilities/tokenbucket.php';
include_once './library/creature.php';

// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$ratelimiter = new TokenBucket($db, $_SERVER['REMOTE_ADDR'], 100, 10);
$data = json_decode(file_get_contents("php://input"));

// Check ip against rate limits
if (!$ratelimiter->consume(1)){
    http_response_code(429);
    die(json_encode(array("message" => "(429) Too many requests.")));
}

// No data validation is really needed here; nothing bad happens with invalid requests

// Encode the db object...
$creature = new Creature($db);

$creature->code = $data->code;

// ... and drop it from the db.
if($creature->delete()) {
    http_response_code(200);
    echo json_encode(array("message" => "(200) creature was deleted."));
} else {
    http_response_code(503);
    die(json_encode(array("error" => "(503) Unable to delete creature.")));
}