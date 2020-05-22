<?php

// Step 2 of chron job:

// Drop all entries in the creatures table which are confirmed to have grown up (growthlevel > 2)
// Drop all entries in the creatures table which are more than 5 days old (5 days is max grow time)
// Drop all entries in the clicks table tied to creatures whcich are the same as ^
// Drop all entries in the clicks table that are more than 1 day old.

include_once (__DIR__).'/../api/db/db.php';

$database = new Database();
$conn = $database->getConnection();
$creature_table = 'creatures';
$click_table = 'userclicks';

// UNIX timestamp for 5 days prior to current time. All entries older than this are deleted
$maxGrowDate = (string) strtotime('-5 day', time());

// UNIX timestamp for 1 day prior to current time. Clicks older than this have refreshed and should be deleted.
$clickRefreshDate = (string) strtotime('-1 day', time());

// Delete click table entries for grown creatures
$query = "DELETE uc.* FROM " . $creature_table . " AS c LEFT OUTER JOIN " . $click_table . " AS uc ON c.code = uc.code 
    WHERE c.growthLevel > 2 OR c.gotten < " . $maxGrowDate;
$stmt = $conn->prepare($query);
$stmt->execute();

// Delete creature tabled entries for grown creatures
$query = "DELETE c.* FROM " . $creature_table . " AS c LEFT OUTER JOIN " . $click_table . " AS uc ON c.code = uc.code 
    WHERE c.growthLevel > 2 OR c.gotten < " . $maxGrowDate;
$stmt = $conn->prepare($query);
$stmt->execute();

//Delete click table entries for expired clicks
$query = "DELETE uc.* FROM " . $click_table . " AS uc WHERE uc.clicked < " . $clickRefreshDate;
$stmt = $conn->prepare($query);
$stmt->execute();



