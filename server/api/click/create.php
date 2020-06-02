<?php
/************************************************************************************** 
 * click/create Internal-API endpoint
 * 
 * Takes: POST {"code" : "code1"}
 * Returns: {"message" : ...}
 * 
 * Description:
 * This endpoint is the main vector for populating the Clicks database table. The Clicks 
 * table is intended to track each individual user's interactions with elements in the Creature
 * table, by associating a user-generated UUID with the Creature's code (primary key) during 
 * interaction events. Timestamps are also recorded during these events - clicks are cleared 
 * after one day has passed by cron.
 * 
 * Frontend-generated UUID is used for this tracking instead of IP or IP-based
 * sessions so that the site works as intended when multiple users are under the same IP.
 * 
 **************************************************************************************/

header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_REFERER']);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// If preflight, return only the headers and not the content
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

// Include database and object files
include_once '../utilities/db.php';
include_once '../utilities/logger.php';
include_once '../utilities/limiter.php';
include_once '../library/click.php';
include_once '../library/creature.php';

// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$ratelimiter = new RateLimiter($db, $_SERVER['REMOTE_ADDR'], 100, 10);
$data = json_decode(file_get_contents("php://input"));

// Check ip against rate limits
// Click operations have a cost of 1/100 tokens (10 per second)
if (!$ratelimiter->consume(1)){
    http_response_code(429);
    die(json_encode(array("message" => "(429) Too many requests.")));
}

// Validate frontend-generated UUID (stored in the 'tfopr-uuid' browser cookie)
if($_COOKIE['tfopr-uuid']!=null && strlen($_COOKIE['tfopr-uuid'])==36) {
    $uuid = $_COOKIE['tfopr-uuid'];
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to log click. UUID token is invalid.")));
}

// Validate POSTed creature code
if(!empty($data->code) && strlen($data->code)==5) {
    $code = $data->code;

    // Check if creature exists in server db
    // (if it doesn't, how could someone click it? This should never happen.)
    $creature = new creature($db);
    $creature->code = $code;
    $creature->readOne();
    if($creature->name==null){
        http_response_code(409);
        die(json_encode(array("message" => "(409) Unable to log click. Creature code does not exist in database.")));
    }
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to log click. Creature code is invalid.")));
}

// If data validation checks are passed, encode the data object...
$click = new Click($db);
$click->uuid = $uuid;
$click->code = $code;
$click->time = (string) time();

// ... and add it to the db table.
if($click->create()){
    $log = new Logger($db);
    $log->ip = $_SERVER['REMOTE_ADDR'];
    $log->logClick();

    http_response_code(201);
    echo json_encode(array("message" => "(201) Creature click was logged."));
} else {
    http_response_code(503);
    die(json_encode(array("error" => "(503) Unable to log creature click.")));
}