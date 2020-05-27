<?php
include_once (__DIR__).'/../config/config.php';
include_once (__DIR__).'/../api/utilities/db.php';
include_once (__DIR__).'/../api/library/flag.php';
include_once (__DIR__).'/../api/library/creature.php';

$database = new Database();
$conn = $database->getConnection();

// Read all creature keys in 'FlaggedCodes'
$flags = new Flag($conn);
$stmt = $flags->readCodes();

if($stmt->rowCount()>0) {
    $codes = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        array_push($codes, $row['code']);
    }

    $curl_out=checkCodes($codes);

    if($curl_out) {
        $creature = new Creature($conn);
        $curl_data = json_decode($curl_out);
            /*[
                {"error":false, "code":"code1", "growthLevel":"1", "isStunted":bool}
                {"error":true,"errorCode":4,"code":"code2"}
            ]*/

        foreach($curl_data as &$packet) {
            if(!$packet->error) {
                $creature->code = $packet->code;
                $creature->growthLevel = $packet->growthLevel;
                $creature->isStunted = $packet->isStunted ? 'true' : 'false';
                $creature->update();
            } else if($packet->error && $packet->errorCode == 4) {
                $creature->code = $packet->code;
                $creature->delete();
            } else {
                echo('Unknown Packet Error: '.'code='.$packet->code.'  errorCode='.$packet->errorCode);
            }
        }

        $flags->clear();
    }
}

function checkCodes($codes_to_query) {
    $handle = curl_init(API_PATH);
    $action = 'multipleCreatures';
    $value = implode(",", $codes_to_query);

    $data = array(
        'key' => API_KEY,
        'action' => $action,
        'var' => $value
    );

    $payload = json_encode($data);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

    try{
        $output = curl_exec($handle);
    } catch(Exception $e) {
        echo('Exception in cron cURL: '.$e->getMessage()."\n");
    }

    if(!curl_errno($handle) && $output!='') {return $output;}
    else {return false;}
}