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
include_once '../db/db.php';
include_once '../utilities/tokenbucket.php';
include_once '../library/session.php';
include_once '../library/creature.php';

// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$ratelimiter = new TokenBucket($db, $_SERVER['REMOTE_ADDR'], 100, 10);
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
                    die(json_encode(array("message" => "(400) Unable to create creatures. One or more codes are invalid.")));
                }
            }
        } else {
            http_response_code(400);
            die(json_encode(array("message" => "(400) Unable to create creatures. Codes array is too large (max 30).")));
        }
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to create creatures. Codes array does not exist.")));
}

// If data validation checks are passed, open/update a session object...
$session = new Session($db);
$session->ip = $_SERVER['REMOTE_ADDR'];
$session->time = time();
$session->updateAndRead();

// ... and validate creature codes against associated cache.
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
    // If all codes are valid, import each of them from cache db to creatures db
    foreach($codes as &$code) {
        $creature->code = $code;
        if(!$creature->importFromCache()) {
            http_response_code(503);
            echo json_encode(array("error" => "(503) Unable to create creature."));
        }
    }
    http_response_code(201);
    echo json_encode(array("message" => "(201) Creatures were created."));
} else {
    http_response_code(404);
    die(json_encode(array("message" => "(404) Unable to create creatures. One or more codes are missing from cache.")));
}