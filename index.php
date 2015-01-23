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
switch ($opts->gets("option")) {
    case "areas":
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
        break;
    case "display":
        $displayRejected = $opts->gets("rejected") == "1";
        $display = new PlacesDisplay($db, $displayRejected);
        $display->display();
        break;
    case "report":
        $gr = $opts->posts("gridref");
        $reporttype = $opts->posts("type");
        $form = new PlacesReportform($db, $gr, $reporttype);
        $form->display();
        break;
    case "processReport":
        $gr = $opts->posts("Report_GR");
        $reporttype = $opts->posts("Report_Type");
        $description = $opts->posts("Report_Text");
        $form = new PlacesReportform($db, $gr, $reporttype);
        $form->process($description);
        break;
    case "details":
        $display = new PlacesDetails($db);
        $display->display($opts->gets("id"), $opts->gets("no"));
        break;
    case "importcsv":
        $file = $opts->gets("file");
        $import = new PlacesImportcsv($db);
        $import->process($file);
        break;
    case "statistics":
        $stats = new PlacesStatistics($db);
        $stats->display();
        break;
    default:
        $homepage = file_get_contents('splash.html');
        echo $homepage;
}
$db->closeConnection();

