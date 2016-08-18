<?php

// options
// index.php normal splash page
// index.php?option=display map display page
// task.php scheduled task to retrieve walks json fee
// index.php?option=details display details of a location
// index.php?option=report report a problem with a location (gridref)
// index.php?option=processReport process report about location girdref
// index.php?option=helppage display a help page
// index.php?option=gridrefpage display help about grid references

error_reporting(-1);
ini_set('display_errors', 'On');
if (file_exists("config.php")) {
    require_once 'config.php';
} else {
    require_once 'configtest.php';
}

require_once 'classes/autoload.php';
Logfile::create("logfiles/testing.log");
$config = new Config();
$db = new PlacesDatabase($config->database);
$db->Connect();
if (!$db->connected()){
    PlacesEmail::send("Index: Unable to connect to database", $db->error());
}
$opts = new Options();

// set up menu
$menuoptions = new PlacesMenuitem("Options", "index.php");
$menugridref = new PlacesMenuitem("Grid References", "index.php?option=gridrefpage");
$menuhelp = new PlacesMenuitem("Help", "index.php?option=helppage");
$menu = new PlacesMenu();
$menu->addItem($menuoptions);
$menu->addItem($menuhelp);
$menu->addItem($menugridref);

switch ($opts->gets("option")) {
        case "display":
        $stars = [];
        $stars[0] = $opts->gets("0star") == "1";
        $stars[1] = $opts->gets("1star") == "1";
        $stars[2] = $opts->gets("2star") == "1";
        $stars[3] = $opts->gets("3star") == "1";
        $stars[4] = $opts->gets("4star") == "1";
        $stars[5] = $opts->gets("5star") == "1";

        $age = $opts->gets("age");
        $display = new PlacesDisplay($db, $stars, $age);
        $display->display($menu);
        break;
    case "details":
        $display = new PlacesDetails($db);
        $display->display($opts->gets("id"), $opts->gets("no"));
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
   case "importcsv":
        $file = $opts->gets("file");
        $import = new PlacesImportcsv($db);
        $import->process($file);
        break;
    case "helppage":
        $template = new Template("html/help.html");
        $template->replaceString("// [[Insert menu]]", $menu->getMenu($menuhelp));
        $template->displayTemplate();
        break;
    case "gridrefpage":
        $template = new Template("html/gridref.html");
        $template->replaceString("// [[Insert menu]]", $menu->getMenu($menugridref));
        $template->displayTemplate();
        break;
    default:
        $template = new Template("html/splash.html");
        $template->replaceString("// [[Insert menu]]", $menu->getMenu($menuoptions));
        $template->displayTemplate();
        break;
}
$db->closeConnection();

