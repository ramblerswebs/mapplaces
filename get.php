<?php

// return ALL places that have a positive score and are within a defined distance of a location

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

$config = new Config();
$db = new PlacesDatabase($config->database);
$db->connect();
if (!$db->connected()) {
    PlacesEmail::send("Task: Unable to connect to database", $db->error());
}
$opts = new Options();
$easting = $opts->gets("easting");
$northing = $opts->gets("northing");
$distance = $opts->gets("dist");
if ($distance == false) {
    $distance = 10; //10Km
}
$maxpoints = $opts->gets("maxpoints");
$age = PlacesFunctions::getAgeDate("5years");
$OSRef = new PHPCoord\OSRef($easting, $northing); //Easting, Northing

$LatLng = $OSRef->toLatLng();
$locations = $db->getPlacesRecords($age, "newer", $easting, $northing, $distance * 1000);
foreach ($locations as $location) {
    $location->D = PlacesFunctions::distance($LatLng->getLat(), $LatLng->getLng(), $location->Lat, $location->Lng);
    $location->D = number_format($location->D, 1, '.', '');
}
$locations=PlacesFunctions::sortOnDistance($locations);
if (count($locations)>$maxpoints){
    $locations=array_slice($locations,0,$maxpoints,true) ;
}

$db->closeConnection();
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
echo json_encode($locations);