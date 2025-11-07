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
        $code = $this->area->getCode();
        $feed = new RamblersFeedWalks($code);
        Logfile::create("logfiles/logfile_area_" . $code . ".log");
        Logfile::writeWhen("Area Code: " . $code . " - " . $this->area->getName());
        Logfile::writeWhen(" ");
        if ($feed->errorCode() == 0) {
            $walks = $feed->getWalks();
            // find one month ahead
            $int = new DateInterval("P1M");
            $onemonth = new DateTime("Now");
            $onemonth->add($int);
            // compare with walk date
            // only add if walk is within one month

            if ($walks != NULL) {
                foreach ($walks as $value) {
                    $data = $feed->getWalkInfo($value);
                    // update if changed since last scan
                    if ($data !== null) {
                        // if ($value->status === 'confirmed') {
                        //   $cwalkdate = substr($value->start_date_time, 0, 19);
                        //   $walkdate = DateTime::createFromFormat(self::TIMEFORMAT, $cwalkdate);
                        if ($data->walkDate <= $onemonth) {
                            $this->processData($data);
                        }
                    }
                }

                // update area last read date
            }
        } else {
            logfile::writeError("Unable to read feed for Area " . $code);
        }
        Logfile::writeWhen("End of processing");
        Logfile::close();
    }

    private function processData($data) {
        $cwalkdate = $data->cwalkdate;
        $points = $data->points;
        foreach ($points as $point) {

            $desc = $point->description;
            if ($point->type == PlacesEnums::MeetingPoint) {
                Logfile::writeWhen($cwalkdate . " Walk(" . $data->id . ") GR:" . $point->gridRef . " Meet , '" . $desc . "'");
                echo "<p>" . $cwalkdate . " Walk(" . $data->id . ") GR:" . $point->gridRef . " Meet , " . $desc . "</p>\n\r";
                $this->db->addPlace($point->type, $data, $point);
            }
            if ($point->type == PlacesEnums::StartingPoint) {
                Logfile::writeWhen($cwalkdate . " Walk(" . $data->id . ") GR:" . $point->gridRef . " Start , '" . $desc . "'");
                echo "<p>" . $cwalkdate . " Walk(" . $data->id . ") GR:" . $point->gridRef . " Start , " . $desc . "</p>\n\r";
                $this->db->addPlace($point->type, $data, $point);
            }
        }
    }
}