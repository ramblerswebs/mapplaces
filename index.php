<?php
// options
// index.php normal splash page
// index.php?option=display map displat page
// index.php?option=areas read area information
// task.php scheduled task to retrieve walks json feed
// index.php?option=statistics
// index.php?option=importcsv&file=file.csv

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
// echo $_SERVER['HTTP_REFERER'];
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
//if ($opts->gets("option") == "getwalks") {
//    $nextarea = $db->getNextArea();
//    $update = new PlacesUpdate($db, $nextarea);
//    $update->processFeed();
//}
if ($opts->gets("option") == "display") {
    $displayRejected = $opts->gets("rejected") == "1";
    $display = new PlacesDisplay($db, $displayRejected);
    $display->display();
}
if ($opts->gets("option") == "report") {
    //echo "REPORT";
    $gr = $opts->posts("gridref");
    $reporttype = $opts->posts("type");
    $form = new PlacesReportform($db,$gr, $reporttype);
    $form->display();
}
if ($opts->gets("option") == "processReport") {
    $gr = $opts->posts("Report_GR");
    $reporttype = $opts->posts("Report_Type");
    $description = $opts->posts("Report_Text");
    $form = new PlacesReportform($db,$gr, $reporttype);
    $form->process($description);
}
if ($opts->gets("option") == null) {
    $homepage = file_get_contents('splash.html');
    echo $homepage;
}
if ($opts->gets("option") == "details") {

    $display = new PlacesDetails($db);
    // echo $opts->gets("no");
    $display->display($opts->gets("id"), $opts->gets("no"));
}
if ($opts->gets("option") == "importcsv") {
    $file = $opts->gets("file");
    $import = new PlacesImportcsv($db);
    $import->process($file);
}
if ($opts->gets("option") == "statistics") {
    
    $stats = new PlacesStatistics($db);
    $stats->display();
}

$db->closeConnection();
//echo $db->status;
