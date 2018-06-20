<?php

error_reporting(-1);
ini_set('display_errors', 'On');
$exepath = dirname(__FILE__);
define('BASE_PATH', dirname(realpath(dirname(__FILE__))));
chdir($exepath);
ini_set('default_socket_timeout', 300); // 300 Seconds = 5 Minutes

if (file_exists("config.php")) {
    require_once 'config.php';
} else {
    require_once 'configtest.php';
}
require_once 'classes/autoload.php';

$config = new Config();
$db = new PlacesDatabase($config->database);
$db->connect();
if (!$db->connected()) {
    PlacesEmail::send("Task: Unable to connect to database", $db->error());
}

$groups = new RamblersOrganisationGroups($db);
$control = new PlacesControl();
// now process a number of Ramblers Areas
$i = 0;
while ($i <= 4) {
    $i+=1;
    $lastAreaProcessed = $control->lastAreaProcessed();
    $nextArea = $groups->nextArea($lastAreaProcessed);
    $lastupdated = RamblersFeedWalks::getDateFileLastUpdated($nextArea->getCode());
    $yesterday = new DateTime("yesterday");
    if ($lastupdated < $yesterday) {
        echo "<p>Processing Area: " . $nextArea->getCode() . "</p>";
        $update = new PlacesUpdate($db, $nextArea);
        $update->processFeed();
    } else {
         echo "<p>Area: " . $nextArea->getCode() . " already processed today</p>";
    }       
    $control->updateLastAreaProcessed($nextArea);
}
// remove items over 10 years old
$db->removeOldLocationRecords();
// remove items were count is over 20
$db->removeMultipleLocations();
$db->closeConnection();
