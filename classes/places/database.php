<?php

/**
 * Description of database
 *
 * @author Chris Vaughan
 */
class PlacesDatabase extends Database {

    //put your code here
    private $tables = ["areas", "places"];
    private $sql = ["CREATE TABLE `areas` (
  `code` char(2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `lastread` date DEFAULT '2000-01-01'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;",
        "CREATE TABLE `places` (
  `walkid` int(11) NOT NULL,
  `startpoint` int(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `gridref` text NOT NULL,
  `northing` int(11) NOT NULL,
  `easting` int(11) NOT NULL,
  `longitude` float NOT NULL DEFAULT '0',
  `latitude` float NOT NULL DEFAULT '0',
  `extras` blob NOT NULL,
  `dateused` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8"];

    function __construct($dbconfig) {
        parent::__construct($dbconfig);
    }

    function loadAreas($areas) {
        // clear area table
        $query = "TRUNCATE TABLE `areas`";
        $ok = parent::runQuery($query);
        if ($ok) {
            foreach ($areas as $area) {
                $names = $area->getNames();
                $values = $area->getValues();
                parent::insertRecord("areas", $names, $values);
            }
        } else {
            echo "Failed to clear areas table";
        }
        // load areas
    }

    function getNextArea() {
        $query = "SELECT * FROM areas ORDER BY lastread ASC LIMIT 1";
        $ok = parent::runQuery($query);
        $area = null;
        if ($ok) {
            while ($row = parent::getResult()->fetch_assoc()) {
                printf($row["code"] . $row["description"] . $row["lastread"]);
                $area = new RamblersOrganisationArea($row["code"], $row["description"]);
                $area->lastread = $row["lastread"];
            }
        }

        /* free result set */
        parent::freeResult();
        return $area;
    }

    function updateAreaLastreadDate($area) {
        $query = "UPDATE `areas` SET `lastread`='[value-1]' WHERE code = '[value-2]'";
        $today = new DateTime("now");
        $todays = $today->format("Y-m-d");
        $query = str_replace("[value-1]", $todays, $query);
        $query = str_replace("[value-2]", $area->code, $query);
        $ok = parent::runQuery($query);
    }

    function addPlace($type, $walk, $point) {
        $id = $walk->id * 10 + $type;
        // delete walk if already there
        $query = "DELETE FROM places WHERE walkid = " . $id;
        // insert new record
        $ok = parent::runQuery($query);
        if ($ok == false) {
            echo "Error deleting record";
        }
        $extras = new PlacesExtras;
        $extras->group = $walk->groupCode;
        $extras->postcode = $point->postcode;
        $extras->latitude = $point->postcodeLatitude;
        $extras->longitude = $point->postcodeLongitude;

        $names = array();
        $names[] = "walkid";
        $names[] = "startpoint";
        $names[] = "name";
        $names[] = "gridref";
        $names[] = "easting";
        $names[] = "northing";
        $names[] = "longitude";
        $names[] = "latitude";
        $names[] = "extras";
        $names[] = "dateused";
        $values = array();
        $values[] = $id;
        $values[] = $type;
        $values[] = $point->description;
        $values[] = $point->gridRef;
        $values[] = $point->easting;
        $values[] = $point->northing;
        $values[] = $point->longitude;
        $values[] = $point->latitude;
        $values[] = json_encode($extras);
        $values[] = $walk->date;
        parent::insertRecord("places", $names, $values);
    }

    function getPlaces() {
        $today = new DateTime("now");
        $todays = $today->format("Y-m-d");
        $query = "SELECT gridref,latitude,longitude,COUNT(*) as count FROM places WHERE dateused < '[todays]' GROUP BY gridref";
        $query = str_replace("[todays]", $todays, $query);
        $ok = parent::runQuery($query);
        if ($ok == true) {
            $result = parent::getResult();
            /* fetch object array */
            while ($row = $result->fetch_row()) {
                //  printf("%s %s %s (%s)\r\n", $row[0], $row[1],$row[2],$row[3]);
                $gr = $row[0];
                $lat = $row[1];
                $long = $row[2];
                $no = $row[3];

                //  echo "var marker = L.marker([".$lat.", ".$long."]);";
                // echo " markerList.push(marker);\r\n";
                echo "addPlace(markerList ,\"" . $gr . "\", " . $no . ", " . $lat . ", " . $long . ");\r\n";
            }
            echo "markers.addLayers(markerList);
		map.addLayer(markers);";
            unset($result);
            parent::freeResult();
        }
    }

    function getDetails($id) {
        $today = new DateTime("now");
        $todays = $today->format("Y-m-d");
        $query = "SELECT name,dateused FROM places WHERE gridref='" . $id . "' AND dateused < '[todays]' ORDER BY dateused DESC";
        $query = str_replace("[todays]", $todays, $query);
        $ok = parent::runQuery($query);
        if ($ok == true) {
            $result = parent::getResult();
            /* fetch object array */
            echo "<p><b>Used         Description</b></p>";
            while ($row = $result->fetch_row()) {
                //  printf("%s %s %s (%s)\r\n", $row[0], $row[1],$row[2],$row[3]);
                $desc = $row[0];
                if ($desc == "") {
                    $desc = "[No description]";
                }
                $lastread = $row[1];
                echo  $lastread . "  " . $desc . "<br/>";
            }
            unset($result);
            parent::freeResult();
        }
    }

    function connect() {
        parent::connect();
        for ($i = 0; $i < 2; $i++) {
            // echo $i;
            parent::createTable($this->tables[$i], $this->sql[$i]);
        }
    }

}
