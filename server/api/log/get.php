<?php
/************************************************************************************** 
 * log/get Internal-API endpoint
 * 
 * Takes: GET
 * Returns: {"weekly": {"uniques": count,"clicks": count,"curls": count,"creatureAdds": count,"creatureRemoves": count},
 *          "allTime": {"clicks": count, "curls": count,"creatureAdds": count,"creatureRemoves": count}}
 * 
 * Description:
 * This endpoint allows the frontend to fetch data contained in the Log_Weekly and Log_Compiled tables,
 * for display in the frontend metrics widget. As this action takes no request data, there are no validation checks,
 * however the rate-limits for requests is set rather high to prevent excessive calls. This endpoint should only
 * be called very infrequently by any given user.
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
include_once '../utilities/limiter.php';
include_once '../library/log.php';
  
// Instantiate objects
$database = new Database();
$db = $database->getConnection();
$ratelimiter = new RateLimiter($db, $_SERVER['REMOTE_ADDR'], 100, 10);
$data = json_decode(file_get_contents("php://input"));

// Check ip against rate limits
// Log fetches are done rarely; they have a token cost of 30/100 (3 per 10s)
if (!$ratelimiter->consume(30)){
    http_response_code(429);
    die(json_encode(array("message" => "(429) Too many requests.")));
}

// Prepare output object
$output = array();
$output['weekly'] = array();
$output['allTime'] = array();

// Create data object and execute query for weekly logs
$logs = new Log($db);

$logs->readWeeklyLogs();
$output['weekly']['uniques'] = $logs->uniques;
$output['weekly']['clicks'] = $logs->clicks;
$output['weekly']['curls'] = $logs->curls;
$output['weekly']['creatureAdds'] = $logs->creatureAdds;
$output['weekly']['creatureRemoves'] = $logs->creatureRemoves;

// Execute query for all-time logs
$logs->readCompiledLogs();
$output['allTime']['clicks'] = $logs->clicks + $output['weekly']['clicks'];
$output['allTime']['curls'] = $logs->curls + $output['weekly']['curls'];
$output['allTime']['creatureAdds'] = $logs->creatureAdds + $output['weekly']['creatureAdds'];
$output['allTime']['creatureRemoves'] = $logs->creatureRemoves + $output['weekly']['creatureRemoves'];

http_response_code(200);
echo json_encode($output); 