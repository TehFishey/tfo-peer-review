<?php
header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// If preflight, return only the headers and not the content
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

// Include database and object files
include_once '../utilities/db.php';
include_once '../utilities/limiter.php';
include_once '../library/curl';
include_once '../library/session.php';
include_once '../library/creature.php';
  
// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$ratelimiter = new RateLimiter($db, $_SERVER['REMOTE_ADDR'], 100, 10);
$data = json_decode(file_get_contents("php://input"));

// Check ip against rate limits
if (!$ratelimiter->consume(30)){
    http_response_code(429);
    die(json_encode(array("message" => "(429) Too many requests.")));
}

// Validate incoming labname exists (allow for both JSON + URI inputs)
if(!empty($_GET['labname'])) {
    $labname = htmlspecialchars(strip_tags($_GET['labname']));
} else if(!empty($data->labname)) {
    $labname = htmlspecialchars(strip_tags($data->labname));
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to fetch lab. Lab name is empty.")));
} 

// If data validation checks are passed, open/update a session object...
$session = new Session($db);
$session->ip = $_SERVER['REMOTE_ADDR'];
$session->time = time();
$session->updateAndRead();

// ... create a new cURL request...
$curl = new TFO_cURL();
$curl->action = 'lab';
$curl->var = $labname;

// ... execute the request ...
$curl->execute();

// If request failed, return error
if($curl->error!=null || $curl->output=='') {
    http_response_code(500);
    die(json_encode(array("error" => "(500) Error communicating with TFO.")));
} 
// Otherwise, parse response into a session cache and then pass along response object
else {
    $result = $curl->output;
    $imports = json_decode($result, true);
    if(!$imports['error']) {
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
            //$creature->isStunted = $item['isStunted'];

            $creature->replaceInCache();
        }
    }

    http_response_code(200);
    echo $result;
}