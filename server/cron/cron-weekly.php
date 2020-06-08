<?php
// Secondary cron script for compiling and clearing logging database tables.
// Intended to only run once per week.

include_once (__DIR__).'/../api/utilities/db.php';

$database = new Database();
$conn = $database->getConnection();
$ip_table_name = 'Log_Uniques';
$rate_limiter_table_name = 'RateLimits';
$weekId = date('Y-W');

$query = "DELETE FROM ".$ip_table_name." WHERE weekId!=:weekId";

$stmt = $conn->prepare($query);
$stmt->bindParam(":weekId", $weekId);
$stmt->execute();

$query = "DELETE FROM ".$rate_limiter_table_name;
$stmt = $conn->prepare($query);
$stmt->execute();