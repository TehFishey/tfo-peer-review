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
include_once '../utilities/db.php';
include_once '../utilities/limiter.php';
include_once '../library/creature.php';

// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$ratelimiter = new RateLimiter($db, $_SERVER['REMOTE_ADDR'], 100, 10);
$data = json_decode(file_get_contents("php://input"));

// Check ip against rate limits
if (!$ratelimiter->consume(5)){
    http_response_code(429);
    die(json_encode(array("message" => "(429) Too many requests.")));
}

// Validate incoming creature code array
if(!empty($data->codes) 
    && is_array($data->codes)
    && sizeof($data->codes) > 0) {
        $codes = $data->codes;
        if(sizeof($data->codes) <= 30) {
            foreach($codes as &$code) {
                if(gettype($code) != 'string' || strlen($code) != 5) {
                    http_response_code(400);
                    die(json_encode(array("message" => "(400) Unable to test creature codes. One or more codes are invalid.")));
                }
            }
        } else {
            http_response_code(400);
            die(json_encode(array("message" => "(400) Unable to test creature codes. Codes array is too large (max 30).")));
        }
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to test creature codes. Codes array does not exist.")));
}

$creature = new Creature($db);
$output = array();
$output['exists'] = array();
$output['found'] = false;

foreach($codes as &$code) {
    $creature->code = $code;
    $output['exists'][$code] = $creature->readOne();
}

if(sizeof($output['exists']) > 0) {
    $output['found'] = true;
}

http_response_code(200);
echo json_encode($output);