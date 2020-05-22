<?php

// Step 1 of chron job:

// Get all creature codes in 'markedkeys' table
// For each code, retrieve the creature from TLO and push it do the 'creatures' table
// Clear 'markedkeys' table afterwards

include_once (__DIR__).'/../config/config.php';
include_once (__DIR__).'/../api/db/db.php';
include_once (__DIR__).'/../api/library/ckey.php';
include_once (__DIR__).'/../api/library/creature.php';

$database = new Database();
$conn = $database->getConnection();

// Read all creature keys in 'markedkeys'
$ckey = new CreatureKey($conn);
$stmt = $ckey->read();

if($stmt->rowCount()>0) {
    $key_arr=array();  
    
    // Add all keys in 'markedkeys' to array
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        array_push($key_arr, $code);
    }

    // For each key in array, cURL that creature from TLO...
    foreach($key_arr as &$arrkey){
        $handle = curl_init(API_PATH);
        $value = $arrkey;

        $data = array(
            'key' => API_KEY,
            'action' => 'creature',
            'var' => $value
        );
    
        $payload = json_encode($data);
    
        curl_setopt($handle, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    
        $output = curl_exec($handle);

        if (curl_errno($handle)) {
            $output = curl_error($handle);
        } else {
            // ... And push its data to 'creature' table.
            $creature = new Creature($conn);
            $data = json_decode($output)->{'0'};

            if(
                !empty($data->code) &&
                !empty($data->imgsrc) &&
                !empty($data->gotten) &&
                !empty($data->name) &&
                !empty($data->growthLevel)
            ){
                $creature->code = $data->code;
                $creature->imgsrc = $data->imgsrc;
                $creature->gotten = $data->gotten;
                $creature->name = $data->name;
                $creature->growthLevel = $data->growthLevel;
              
                $creature->replace();
            }
        }
        curl_close($handle);
    }

    // clear 'markedkeys' table afterwards
    $ckey->clear();
}