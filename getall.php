<?php

// return ALL places that have a positive score

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

$cache = new Cache('getall');
$result = $cache->getCachedString();
if ($result === null) {
    $config = new Config();
    $db = new PlacesDatabase($config->database);
    $db->connect();
    if (!$db->connected()) {
        PlacesEmail::send("Task: Unable to connect to database", $db->error());
    }
    $age = PlacesFunctions::getAgeDate("5years");
    $locations = $db->getAllPlaces($age);
    foreach ($locations as $location) {
        unset($location->D);
    }
    $db->closeConnection();
    $result=json_encode($locations);
    $cache->saveString($result);
}

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
echo $result;
