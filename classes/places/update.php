<?php

/**
 * Description of update
 *
 * @author Chris Vaughan
 */
class PlacesUpdate {

    private $area;
    private $db;

    const TIMEFORMAT = "Y-m-d\TH:i:s";

    public function __construct($database, $area) {
        $this->area = $area;
        $this->db = $database;
    }

    public function processFeed() {
        $code=$this->area->getCode() ;
        $feed = new RamblersFeedWalks($code);
        Logfile::create("logfiles/logfile_area_" . $code . ".log");
        Logfile::writeWhen("Area Code: " . $code . " - " . $this->area->getName());
        Logfile::writeWhen(" ");
        if ($feed->errorCode() == 0) {
            $walks = $feed->getJson();
            // find one month ahead
            $int = new DateInterval("P1M");
            $onemonth = new DateTime("Now");
            $onemonth->add($int);
            // compare with walk date
            // only add if walk is within one month

            if ($walks != NULL) {
                foreach ($walks as $value) {
                    // update if changed since last scan
                    $cwalkdate = substr($value->date, 0, 19);
                    $walkdate = DateTime::createFromFormat(self::TIMEFORMAT, $cwalkdate);

                    if ($walkdate <= $onemonth) {
                        $this->processPoints($value);
                    }
                }
                // update area last read date
            }
        } else {
            logfile::writeError("Unable to read feed for Area ".$code);
        }
        Logfile::writeWhen("End of processing");
        Logfile::close();
    }

    private function processPoints($walk) {
        // CANCELLED WALKS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $cwalkdate = substr($walk->date, 0, 10);
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

                    Logfile::writeWhen($cwalkdate . " Walk(" . $walk->id . ") GR:" . $point->gridRef . " Meet , '" . $desc . "'");
                    $this->db->addPlace(PlacesEnums::MeetingPoint, $walk, $point);
                }
                if ($point->typeString == "Start") {
                    Logfile::writeWhen($cwalkdate . " Walk(" . $walk->id . ") GR:" . $point->gridRef . " Start, '" . $desc . "'");
                    $this->db->addPlace(PlacesEnums::StartingPoint, $walk, $point);
                }
            }
        }
    }

}
