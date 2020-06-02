<?php
include_once (__DIR__).'/../api/utilities/db.php';

$database = new Database();
$conn = $database->getConnection();
$creature_table = 'Creatures';
$click_table = 'Clicks';
$session_table = 'Sessions';
$session_cache_table = 'SessionCache';

/*********************************************************************************************************/
// INVALID CREATURES (and associated clicks)

// UNIX timestamp for 5 days prior to current time.
// On TFO, 5 days is the maximum time it can take for a capsule to grow into an adult.
// Therefore, all creatures with 'gotten' lower than 5 days ago can be safely removed.
$maxGrowDate = (string) strtotime('-5 day', time());

// DELETE CLICK TABLE ENTRIES FOR GROWN CREATURES
// First delete the Clicks entries. If we did Creatures first, we'd have nothing to map clicks to. 
$query = "DELETE clk.* FROM ".$creature_table." AS c LEFT OUTER JOIN ".$click_table." AS clk ON c.code = clk.code 
    WHERE c.growthLevel > 2 OR c.isStunted = 'true' OR c.gotten < " . $maxGrowDate;
$stmt = $conn->prepare($query);
$stmt->execute();

// DELETE CREATURE TABLE ENTRIES FOR GROWN CREATURES
// Delete all known adults, presumed adults, and stunted creatures from the Creatures table.
$query = "DELETE FROM ".$creature_table. 
    " WHERE growthLevel > 2 OR isStunted = 'true' OR gotten < " . $maxGrowDate;
$stmt = $conn->prepare($query);
$stmt->execute();

// Note: we DO NOT delete click table entries that are have no associated creature entries.
// This is because it's possible for users to remove/re-add creature entries. In these cases, we want clicks
// preserved until they roll over at age = 1 day.

/*********************************************************************************************************/
// EXPIRED CLICKS

// UNIX timestamp for 1 day prior to current time.
// On TFO, click rewards are reset 1 day after the click occured.
// Therefore, all clicks with 'time' lower than 1 day ago should be removed.
$clickRefreshDate = (string) strtotime('-1 day', time());

// DELETE CLICK TABLE ENTRIES FOR EXPIRED CLICKS
// Delete Clicks entries that are more than one day old.
$query = "DELETE FROM ".$click_table." 
    WHERE time < ".$clickRefreshDate;
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