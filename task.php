<?php

//
// Process walks to retrieve meeting and start locations and add them to the database
//

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
spl_autoload_register('autoload');
include "PHPCoord-2.1/TransverseMercator.php";
include "PHPCoord-2.1/LatLng.php";
include "PHPCoord-2.1/OSRef.php";
include "PHPCoord-2.1/RefEll.php";
$cc=get_declared_classes();

$config = new Config();
$db = new PlacesDatabase($config->database);
$db->connect();
if (!$db->connected()) {
    PlacesEmail::send("Task: Unable to connect to database", $db->error());
    die();
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
        echo "<details>";
        echo "<summary>Processing of Area: " . $nextArea->getCode() . "</summary>\r\n";
        $update = new PlacesUpdate($db, $nextArea);
        $update->processFeed();
         echo "</details>";
    } else {
         echo "<p>Area: " . $nextArea->getCode() . " already processed today</p>\r\n";
    }       
    $control->updateLastAreaProcessed($nextArea);
}
// remove items over 10 years old
$db->removeOldLocationRecords();
// remove items where count is over 20
$db->removeMultipleLocations();
$db->closeConnection();
// getall cache is now invalid
$cache = new Cache('getall');
$cache->deleteCachedString();