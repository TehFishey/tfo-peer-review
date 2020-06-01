<?php
// Primary cron script for maintaining site functions (updating DB's with new
// info when necessary, clearing aged-out or unnecessary DB entries, etc.) Intended
// to be run frequently - (every 5-30 minutes, reccomended 10).

include_once (__DIR__).'/update-db.php';
include_once (__DIR__).'/flush-db.php';