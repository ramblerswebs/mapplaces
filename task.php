<?php

error_reporting(-1);
ini_set('display_errors', 'On');
if (file_exists("config.php")) {
    require_once 'config.php';
} else {
    require_once 'configtest.php';
}
require_once 'classes/autoload.php';

$config = new Config();
$db = new PlacesDatabase($config->database);
$db->Connect();

// Logfile::create("logfiles/testing.log");

$nextarea = $db->getNextArea();
$update = new PlacesUpdate($db, $nextarea);
$update->processFeed();
echo "End";



$db->closeConnection();
//echo $db->status;