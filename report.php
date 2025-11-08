<?php

// update a place with either a like or dislike record

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

$config = new Config();
$opts = new Options();

$gr = $opts->gets("gr");
$reporttype = $opts->gets("type");
$score = 0;
switch ($reporttype) {
    case "like":
        $desc = "User like " . PlacesFunctions::getUserIP();
        $score = 1;
        break;
    case "dislike":
        $desc = "User dislike " . PlacesFunctions::getUserIP();
        $score = -1;
        break;
}
$out = false;
If ($score !== 0 AND $gr !== null) {
    $type = PlacesEnums::FromUserReport;
    $db = new PlacesDatabase($config->database);
    $db->connect();
    if (!$db->connected()) {
        PlacesEmail::send("Task: Unable to connect to database", $db->error());
    } else {
        $out = $db->addReport($type, $gr, $score, $desc);
        $db->closeConnection();
    }
}
// getall cache is now invalid
$cache = new Cache('getall');
$cache->deleteCachedString();

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
echo json_encode($out);
