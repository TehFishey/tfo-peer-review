<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// if preflight, return only the headers and not the content
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit; }

//API for forwarding frontend requests to finaloutpost.net and returning results

$url = "https://finaloutpost.net/usersc/plugins/apibuilder/examples/labLoad.php";
$key = "G7I1Z-SQ0UQ-5EBC3-DECB3-9A6";

//request Json should include Action and Value props
$request = json_decode(file_get_contents("php://input"));
$action = $request->action;    //valid actions are "lab" and "creature"
$value = $request->value;      //value is username for "lab", code for "creature"

$handle = curl_init($url);
 
$data = array(
    'key' => $key,
    'action' => $action,
    'var' => $value
);

$payload = json_encode($data);

//Set up the cURL request
curl_setopt($handle, CURLOPT_POSTFIELDS, $payload);
curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

$output = curl_exec($handle);
if (curl_errno($handle)) {
    $output = curl_error($handle);
}

curl_close($handle);

echo $output;

?>