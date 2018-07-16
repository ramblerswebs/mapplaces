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
$opts = new Options();

$display = new PlacesDetails($db);
$out=$display->displayNew( $opts->gets("gr"));
$db->closeConnection();
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
echo json_encode($out);