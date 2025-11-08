<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of walks
 *
 * @author Chris Vaughan
 */
class RamblersFeedWalks {

    private $json;
    private $error = 0;
    private $urlBase = 'https://walks-manager.ramblers.org.uk/api/volunteers/walksevents?&types=group-walk';
    // https://walks-manager.ramblers.org.uk/api/volunteers/walksevents?&types=group-walk&api-key=853aa876db0a37ff0e6780db2d2addee&groups=er05
    private $apiKey = '853aa876db0a37ff0e6780db2d2addee';

    const TIMEFORMAT = "Y-m-d\TH:i:s";

    //put your code here
    public function __construct($areacode) {
        $now = new DateTime('now');
        $start = new DateTime();
        $start->modify('-3 weeks');
        $dateLimit = '&date_end=' . $now->format('Y-m-d') . '&date=' . $start->format('Y-m-d');
        $feedurl = $this->urlBase . '&api-key=' . $this->apiKey . '&groups=' . $areacode . $dateLimit;
        $contents = file_get_contents($feedurl);
        if ($contents != "") {
            $this->json = json_decode($contents);
            file_put_contents("feed/area_" . $areacode . ".json", $contents);
        } else {
            $this->error = 1;
        }
    }

    public static function getDateFileLastUpdated($areacode) {
        $file = "feed/area_" . $areacode . ".json";
        return PlacesFunctions::getDateFileLastUpdated($file);
    }

    public function getJson() {
        return $this->json;
    }

    public function getWalks() {
        return $this->json->data;
    }

    public function getWalkInfo($walk) {
        if ($walk->start_date_time === null) {
            return null;
        }
        if ($walk->status !== 'confirmed') {
            return null;
        }
        if ($walk->start_location === null) {
            return null;
        }
        $cwalkdate = substr($walk->start_date_time, 0, 19);
        $walkdate = DateTime::createFromFormat(self::TIMEFORMAT, $cwalkdate);
        $item = new stdClass();
        $item->id = $walk->id;
        $item->groupCode = $walk->group_code;
        $item->walkDate = $walkdate;
        $item->cwalkdate = $cwalkdate;
        $item->points = [];

        if ($walk->meeting_location !== null) {
            $point = $this->getPoint($walk->meeting_location, PlacesEnums::MeetingPoint);
            if ($point !== null) {
                $item->points[] = $point;
            }
        }

        if ($walk->start_location !== null) {
            $point = $this->getPoint($walk->start_location, PlacesEnums::StartingPoint);
            if ($point !== null) {
                $item->points[] = $point;
            }
        }
        return $item;
    }

    private function getPoint($location, $type) {

        $point = new PlacesPoint();
        $point->type = $type;
        $point->gridRef = $location->grid_reference_6;
        if ($point->gridRef === '') {
            return null;
        }
        $point->latitude = $location->latitude;
        $point->longitude = $location->longitude;

        // Convert a grid reference string to OSGB easting/northing
        $osRef = PHPCoord\OSRef::fromSixFigureReference($point->gridRef);
        $point->easting = $osRef->getX();
        $point->northing = $osRef->getY();

        $desc = strip_tags($location->description);
        $desc = str_replace("\r", "", $desc);
        $desc = str_replace("\n", "", $desc);
        $point->description = str_replace("'", "", $desc);

        return $point;
    }

    public function errorCode() {
        return $this->error;
    }
}

//"start_date_time": "2025-11-05T09:00:00",
//      "end_date_time": "2025-11-05T15:30:00",
//      "meeting_date_time": "2025-11-05T08:00:00",
//      "start_location": {
//        "latitude": 54.38645,
//        "longitude": -3.038527,
//        "grid_reference_6": "SD326994",
//        "grid_reference_8": "SD32659949",
//        "grid_reference_10": "SD3265499492",
//        "postcode": "LA21 8DP",
//        "description": "Tarn Hows NT car park. The one with the coffee shop not the other one",
//        "w3w": "juggles.organic.trendy"
//      },
//      "end_location": null,
//      "meeting_location": {
//        "latitude": 54.658157,
//        "longitude": -2.735034,
//        "grid_reference_6": "NY526294",
//        "grid_reference_8": "NY52672947",
//        "grid_reference_10": "NY5267829477",
//        "postcode": "CA11 8RQ",
//        "description": "PRC",
//        "w3w": "thickened.undercuts.emperor"
//      },