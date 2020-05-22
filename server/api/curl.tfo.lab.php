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
include_once './library/curl.tfo.php';
  
// Instantiate objects
$data = json_decode(file_get_contents("php://input"));

// Validate incoming labname exists
if(!empty($data->labname)) {
    $labname = $data->labname;
} else {
    http_response_code(400);
    die(json_encode(array("message" => "(400) Unable to fetch lab. Lab name is empty.")));
}

// If data validation checks are passed, encode the cURL object...
$curl = new TFO_cURL();
$curl->action = 'lab';
$curl->var = $labname;

// ... execute the request ...
$curl->execute();

// If request failed, return error
if($curl->error) {
    http_response_code(500);
    die(json_encode(array("error" => "(500) Error communicating with TFO.")));
} 
// Otherwise return response object
else {
    http_response_code(200);
    echo $curl->output;
}