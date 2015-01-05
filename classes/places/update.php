<?php

/**
 * Description of update
 *
 * @author Chris Vaughan
 */
class PlacesUpdate {

    private $area;
    private $db;

    function __construct($database, $area) {
        $this->area = $area;
        $this->db = $database;
    }

    function processFeed() {
        $feed = new RamblersFeedWalks($this->area);
        Logfile::create("logfiles/logfile_area_" . $this->area->code . ".log");
        Logfile::writeWhen("Area Code: " . $this->area->code." - ".$this->area->description);
        Logfile::writeWhen(" ");
        Logfile::writeWhen("Lastread: " . $this->area->lastread);
        $walks = $feed->json;
        $stats = new RamblersFeedStatistics($walks);
        $stats->Display();
        Logfile::writeWhen("Walkid, Type, Description");

        if ($walks != NULL) {
            foreach ($walks as $value) {
                // update if changed since last scan
                $lastchanged = $value->dateUpdated;
                if ($lastchanged > $this->area->lastread) {

                    $this->processPoints($value);
                }
            }
            // update area last read date
        }
        $this->db->updateAreaLastreadDate($this->area);
        Logfile::writeWhen("End of processing");
        Logfile::close();
    }

    private function processPoints($walk) {

        $points = $walk->points;
        foreach ($points as $point) {
           // echo $point->gridRef . " " . $point->description;
           // echo "<br/>";
            if ($point->showExact == true) {
                $desc = strip_tags($point->description);
                $desc = str_replace("\r", "", $desc);
                $desc = str_replace("\n", "", $desc);
                $desc = str_replace("'", "", $desc);
                if ($point->typeString == "Meeting") {

                    Logfile::writeWhen($walk->id . ", Meet , '" . $desc . "'");
                    $this->db->addPlace(0, $walk, $point);
                }
                if ($point->typeString == "Start") {
                    Logfile::writeWhen($walk->id . ", Start, '" . $desc . "'");
                    $this->db->addPlace(1, $walk, $point);
                }
            }
        }
    }

}
