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
//echo $db->status;
$opts = new Options();
Logfile::create("logfiles/testing.log");

if ($opts->gets("option") == "areas") {
    $areas = new RamblersOrganisationAreas();
    $file = "data/areacodes.xml";
    $values = $areas->readAreaCodes($file);
    // print_r($values);
    foreach ($values as $value) {
        echo "............................";
        echo $value->code;
        echo "  ";
        echo $value->description;
        echo "<br />";
    }
    $db->loadAreas($values);
}
if ($opts->gets("option") == "getwalks") {
    $nextarea = $db->getNextArea();
    $update = new PlacesUpdate($db, $nextarea);
    $update->processFeed();
}
if ($opts->gets("option") == "display") {
    $display = new PlacesDisplay($db);
    $display->display();
}
if ($opts->gets("option") == null) {
    $homepage = file_get_contents('splash.html');
    echo $homepage;
}
if ($opts->gets("option") == "details") {

    $display = new PlacesDetails($db);
    $display->display($opts->gets("id"));
}

$db->closeConnection();
//echo $db->status;
