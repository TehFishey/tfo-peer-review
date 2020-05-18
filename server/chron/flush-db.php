<?php

// Step 2 of chron job:

// Drop all entries in the creatures table which are confirmed to have grown up (growthlevel > 2)
// Drop all entries in the creatures table which are more than 5 days old (5 days is max grow time)

include_once '../api/internal/db/db.php';

$database = new Database();
$conn = $database->getConnection();
$table_name = 'creatures';

// growthLevel represents the growth stage of the creature. 3 is adult.
$query = "DELETE FROM " . $table_name . " WHERE growthLevel > 2";
$stmt = $conn->prepare($query);
$stmt->execute();

// gotten is the unix timestamp of when the creature was aquired.
// if this stamp is lower than the current time - 5 days, the creature will always be grown.
$maxGrowDate = strtotime('-5 day', time()); 
$query = "DELETE FROM " . $table_name . " WHERE gotten < " . $maxGrowDate;
$stmt = $conn->prepare($query);
$stmt->execute();
