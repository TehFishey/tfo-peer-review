<?php

// Step 2 of chron job:

// Drop all entries in the creatures table which are confirmed to have grown up (growthlevel > 2)
// Drop all entries in the creatures table which are more than 5 days old (5 days is max grow time)

include_once '../api/internal/db/db.php';

$database = new Database();
$conn = $database->getConnection();
$creature_table = 'creatures';
$click_table = 'userclicks';

// UNIX timestamp for 5 days prior to current time. All entries older than this are deleted
$maxGrowDate = (string) strtotime('-5 day', time()); 


$query = "DELETE ip.* FROM " . $creature_table . " AS c LEFT OUTER JOIN " . $click_table . " AS ip ON c.code = ip.code 
    WHERE c.growthLevel > 2 OR c.gotten < " . $maxGrowDate;
$stmt = $conn->prepare($query);
$stmt->execute();


$query = "DELETE c.* FROM " . $creature_table . " AS c LEFT OUTER JOIN " . $click_table . " AS ip ON c.code = ip.code 
    WHERE c.growthLevel > 2 OR c.gotten < " . $maxGrowDate;
$stmt = $conn->prepare($query);
$stmt->execute();



