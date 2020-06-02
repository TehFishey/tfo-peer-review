<?php
/************************************************************************************** 
 * creature/get Internal-API endpoint
 * 
 * Takes: GET uri: ?count=int OR POST {"count" : "int"}
 * Returns: { "found" : bool, "creatures" : [{creature1}, {creature2}...] }
 * 
 * Description:
 * This endpoint allows users to get sets of object data regarding entries in the Creatures
 * database table. The number of objects to be retrieved is defined by the "count" property 
 * (default 1, max 100). During retrieval, Creature codes (primary keys) are checked against 
 * codes associated with the user's UUID in the Clicks table (see click/create.php), so that the 
 * endpoint will only return creatures that users have not interacted with in the past day (as fits
 * the purpose of this site). 
 * 
 * Frontend-generated UUID is used for these checks instead of IP or IP-based
 * sessions so that the site works as intended when multiple users are under the same IP.
 * 
 **************************************************************************************/

header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_REFERER']);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET POST");
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

// Validate frontend-generated UUID (stored in the 'tfopr-uuid' browser cookie)
if($_COOKIE['tfopr-uuid']!=null && strlen($_COOKIE['tfopr-uuid'])==36) {
    $uuid = $_COOKIE['tfopr-uuid'];
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to get creatures. UUID token is invalid.")));
}

// How many creatures to fetch? (between 1 and 100; default 1)
// Allows for both JSON and URI inputs, for ease of development
if(!empty($_GET['count'])) {
    $count = $_GET['count'];
    if($count>60) $count = 100;
    if($count<1) $count = 1;;
} else if(!empty($data->count)) {
    $count = $data->count;
    if($count>60) $count = 100;
    if($count<1) $count = 1;
} else $count = 1;

// Check ip against rate limits (done further down to scale tokens to $count)
// Get operations have a token cost of 1 per 2 entries (20 entries per second)
if (!$ratelimiter->consume(ceil($count/2))){
    http_response_code(429);
    die(json_encode(array("message" => "(429) Too many requests.")));
}

// Execute query and prepare return objects
$creature = new Creature($db);
$stmt = $creature->readSet($uuid, $count);
$output = array();
$output["found"] = '';
$output["creatures"] = array();

// If 0 records found, return {found : false}
if(!($stmt->rowCount()>0)) {
    $output["found"] = 'false';

    http_response_code(200);
    echo json_encode($output);
} else {
    // Otherwise, build an array of creatures...
    $output["found"] = 'true';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $item=array(
            "code" => $code,
            "imgsrc" => $imgsrc,
            "gotten" => $gotten,
            "name" => $name,
            "growthLevel" => $growthLevel
        );
        // Push them to {creatures : }, and return.
        array_push($output["creatures"], $item);
    }
    
    http_response_code(200);
    echo json_encode($output);  
}