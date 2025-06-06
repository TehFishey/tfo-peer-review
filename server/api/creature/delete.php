<?php
/************************************************************************************** 
 * creature/delete Internal-API endpoint
 * 
 * Takes: POST {"codes" : ["code1","code2","code3", ...]}
 * Returns: {"message" : ...}
 * 
 * Description:
 * This endpoint allows users to remove select entries from the Creatures table of the
 * the SQL database.
 * 
 * For a user to legally access this endpoint, they should have already completed a creature/fetch
 * request during their current session. Creatures acted on by this input should only be those which
 * they retrieved from via that TLO cURL request. 
 * 
 * This endpoint expects an array of creature codes identifying rows to be removed from 
 * Creatures. Input codes are validated against the user's ip-based session id, 
 * so sessions can only operate on creatures that they personally imported to the cache.
 * 
 **************************************************************************************/

//header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
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
include_once '../utilities/remoteaddress.php';
include_once '../utilities/logger.php';
include_once '../utilities/limiter.php';
include_once '../library/session.php';
include_once '../library/creature.php';

// Instantiate objects
$ip = new RemoteAddress();
$database = new Database();
$db = $database->getConnection();
$ratelimiter = new RateLimiter($db, $ip->getIpAddress(), 100, 10);
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
                    die(json_encode(array("message" => "(400) Unable to delete creatures. One or more codes are invalid.")));
                }
            }
        } else {
            http_response_code(400);
            die(json_encode(array("message" => "(400) Unable to delete creatures. Codes array is too large (max 30).")));
        }
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to delete creatures. Codes array does not exist.")));
}

// If data validation checks are passed, create a session object and run get/create on the sessions table.
$session = new Session($db);
$session->ip = $ip->getIpAddress();
$session->time = time();
$session->updateAndRead();

// Validate creature codes against cached objects associated with active session.
$creature = new Creature($db);
$creature->session = $session->sessionId;
$stmt = $creature->readCachedCodes();
$cachedCodes = array();

if($stmt->rowCount()>0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        array_push($cachedCodes, $row['code']);
    }
}

if(!array_diff($codes, $cachedCodes)) {
    $log = new Logger($db);
    $log->ip = $ip->getIpAddress();
    $log->weekId = date('Y-W');
    // If all codes exist in the session's cache, it's safe to delete them.
    foreach($codes as &$code) {
        $creature->code = $code;
        if(!$creature->delete()) {
            http_response_code(503);
            echo json_encode(array("error" => "(503) Unable to delete creature."));
        } else {
            $log->logRemove();
        }
    }
    http_response_code(201);
    echo json_encode(array("message" => "(201) Creatures were deleted."));
} 

// Otherwise, the user is doing something dirty or has timed out.
else {
    http_response_code(404);
    die(json_encode(array("message" => "(404) Unable to delete creatures. One or more codes are missing from cache.")));
}