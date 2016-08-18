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
    echo "<p>Processing Area: " . $nextArea->getCode() . "</p>";
    $update = new PlacesUpdate($db, $nextArea);
    $update->processFeed();
    $control->updateLastAreaProcessed($nextArea);
}
// remove items over 10 years old
$db->removeOldLocationRecords();
// remove items were count is over 20
$db->removeMultipleLocations();
$db->closeConnection();
