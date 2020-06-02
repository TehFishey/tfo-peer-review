<?php
/************************************************************************************** 
 * creature/test Internal-API endpoint
 * 
 * Takes: POST {"codes" : ["code1","code2","code3", ...]}
 * Returns: {"exists" : [{"code1" : bool}, {"code2" : bool}, {"code3" : bool}, ...]}
 * 
 * Description:
 * This endpoint allows users to check for the existence of Creatures table entries matching 
 * an input set of creature codes (primary keys). The result is used by the frontend to show
 * the user which cached/retrieved creatures (see creature/fetch.php) are candidates for addition or
 * removal from the Creatures table.
 * 
 * For a user to legally access this endpoint, they should have already completed a creature/fetch
 * request during their current session. Creatures acted on by this input should only be those which
 * they retrieved from via that TLO cURL request. Unlike in creature/create and creature/delete, this
 * critera is not actively enforced, due to the benign nature of the requests performed.
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

// Include database and object file
include_once '../utilities/db.php';
include_once '../utilities/logger.php';
include_once '../utilities/limiter.php';
include_once '../library/creature.php';

// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$ratelimiter = new RateLimiter($db, $_SERVER['REMOTE_ADDR'], 100, 10);
$data = json_decode(file_get_contents("php://input"));

// Check ip against rate limits
// Delete operations have a cost of 10/100 tokens (1 per second)
if (!$ratelimiter->consume(10)){
    http_response_code(429);
    die(json_encode(array("message" => "(429) Too many requests.")));
}

// Validate POSTed creature code array
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

// If data validation checks are passed, check each creature code against the Creatures table.
$creature = new Creature($db);
$output = array();
$output['exists'] = array();

// Push {exists: [{code, true/false}]} for each creature code.
foreach($codes as &$code) {
    $creature->code = $code;
    $output['exists'][$code] = $creature->readOne();
}

http_response_code(200);
echo json_encode($output);