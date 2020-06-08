<?php
// Secondary cron script for clearing clicks at 6:00am (est) every day.
// This is done in keeping with how FinalOutpost.net resets its own click counters.

// This should be run once daily at the server time that corresponds to the above.

include_once (__DIR__).'/../api/utilities/db.php';

$database = new Database();
$conn = $database->getConnection();
$click_table = 'Clicks';

$query = "DELETE FROM ".$click_table;
$stmt = $conn->prepare($query);
$stmt->execute();