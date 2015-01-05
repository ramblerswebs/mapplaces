<?php

error_reporting(-1);
ini_set('display_errors', 'On');
require_once 'config.php';
require_once 'classes/autoload.php';

$config = new Config();
$db = new PlacesDatabase($config->database);
$db->Connect();
//echo $db->status;
$opts = new Options();
// Logfile::create("logfiles/testing.log");


//if ($opts->gets("option") == "getwalks") {
    $nextarea=$db->getNextArea();
    $update=new PlacesUpdate($db,$nextarea);
    $update->processFeed();
//}


$db->closeConnection();
//echo $db->status;


