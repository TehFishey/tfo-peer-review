<?php
/************************************************************************************** 
 * creature/fetch Internal-API endpoint
 * 
 * Takes: GET uri: ?labname=name OR POST {"labname" : "name"}
 * Returns: See: https://docs.google.com/document/d/1tRmDw40_VF42uucAZXwYXFfK3qbxM4jaK4YLAjSYFCE/edit
 * 
 * Description:
 * This endpoint preforms 'lab' action cURL requests on TLO's external API. It allows users to fetch
 * all growing creatures associated with the input TLO username, or "labname".
 * 
 * Creature objects fetched via this endpoint are used in two ways. First, they are imported into
 * the SessionCache SQL table, and marked with the user's ip-based session id. After this, the entire
 * response object from TLO is forwarded to the frontend for display/parsing.
 * 
 * Creatures are stored in the SessionCache for the duration of the user's Session (periodically checked
 * by cron.) While in the cache, users can choose to import the creatures into the Creatures table
 * via the creature/create endpoint or to remove creatures with matching codes from the Creatures table
 * via the creature/delete endpoint.
 * 
 **************************************************************************************/

//header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_REFERER']);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// If preflight, return only the headers and not the content
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

// Include database and object files
include_once '../utilities/db.php';
include_once '../utilities/logger.php';
include_once '../utilities/limiter.php';
include_once '../library/curl.php';
include_once '../library/session.php';
include_once '../library/creature.php';
  
// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$ratelimiter = new RateLimiter($db, $_SERVER['REMOTE_ADDR'], 100, 10);
$data = json_decode(file_get_contents("php://input"));

// Check ip against rate limits
// cURL operations have a token cost of 30/100 (3 per 10s)
if (!$ratelimiter->consume(30)){
    http_response_code(429);
    die(json_encode(array("message" => "(429) Too many requests.")));
}

// Validate incoming labname; if valid, sanitize (to prep for cURL).
// Allows for both JSON and URI inputs, for ease of development
if(!empty($_GET['labname'])) {
    $labname = htmlspecialchars(strip_tags($_GET['labname']));
} else if(!empty($data->labname)) {
    $labname = htmlspecialchars(strip_tags($data->labname));
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to fetch lab. Lab name is empty.")));
} 

// If data validation checks are passed, create a session object and run get/create on the sessions table.
$session = new Session($db);
$session->ip = $_SERVER['REMOTE_ADDR'];
$session->time = time();
$session->updateAndRead();

// Create a new cURL request...
$curl = new TFO_cURL();
$curl->action = 'lab';
$curl->var = $labname;

// ... and execute it.
$curl->execute();

// Log that a Curl request was *attempted* (this is done regardless of output)
$log = new Logger($db);
$log->ip = $_SERVER['REMOTE_ADDR'];
$log->weekId = date('Y-W');
$log->logCurl();

// If request failed, return error
if($curl->error!=null || $curl->output=='') {
    http_response_code(500);
    die(json_encode(array("error" => "(500) Error communicating with TFO.")));
}

// Otherwise, parse out the response ...
else {
    $result = $curl->output;
    $imports = json_decode($result, true);

    // If imports['error'] is true, there won't be any creatures to import.
    if(!$imports['error']) {

        // Log labname to db only AFTER it's confirmed to be a valid import
        $log->logLab($labname);

        // Unset error information and decode creatures; import into SessionCache
        unset($imports['error']);
        unset($imports['errorCode']);
        $creature = new Creature($db);
        $creature->session = $session->sessionId;
        foreach ($imports as &$item) {
            $creature->code = $item['code'];
            $creature->imgsrc = $item['imgsrc'];
            $creature->gotten = $item['gotten'];
            $creature->name = $item['name'];
            $creature->growthLevel = $item['growthLevel'];

            // The TFO API usually doesn't return isStunted value (if true, it shouldn't return the creature at all)
            if(!empty($item['isStunted']))
                $creature->isStunted = $item['isStunted'];
            else $creature->isStunted = "false";

            $creature->replaceInCache();
        }
    }

    // Whatever the response from TFO was, pass it verbatim to the client after we're done with it.
    http_response_code(200);
    echo $result;
}