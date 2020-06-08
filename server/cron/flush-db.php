<?php
include_once (__DIR__).'/../api/utilities/db.php';

$database = new Database();
$conn = $database->getConnection();
$creature_table = 'Creatures';
$session_table = 'Sessions';
$session_cache_table = 'SessionCache';

/*********************************************************************************************************/
// INVALID CREATURES

// UNIX timestamp for 5 days prior to current time.
// On TFO, 5 days is the maximum time it can take for a capsule to grow into an adult.
// Therefore, all creatures with 'gotten' lower than 5 days ago can be safely removed.
$maxGrowDate = (string) strtotime('-5 day', time());

// DELETE CREATURE TABLE ENTRIES FOR GROWN CREATURES
// Delete all known adults, presumed adults, and stunted creatures from the Creatures table.
$query = "DELETE FROM ".$creature_table. 
    " WHERE growthLevel > 2 OR isStunted = 'true' OR gotten < " . $maxGrowDate;
$stmt = $conn->prepare($query);
$stmt->execute();

/*********************************************************************************************************/
// EXPIRED Sessions

// UNIX timestamp for 10 minutes prior to current time.
// Sessions/SessionCaches should roll over pretty quick; this will let us remove them if they are 10+ minutes old.
$sessionExpired = (string) strtotime('-10 minutes', time());

// DELETE SESSION TABLE ENTRIES FOR EXPIRED SESSIONS
$query = "DELETE FROM ".$session_table." 
    WHERE time < ".$sessionExpired;
$stmt = $conn->prepare($query);
$stmt->execute();

// DELETE SESSIONCACHE TABLE ENTRIES FOR MISSING SESSIONS
$query = "DELETE cache.* FROM ".$session_cache_table." AS cache LEFT JOIN ".$session_table." AS ses ON cache.sessionId = ses.sessionId 
    WHERE ses.sessionId IS NULL";
$stmt = $conn->prepare($query);
$stmt->execute();