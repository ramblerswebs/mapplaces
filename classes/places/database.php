<?php

/**
 * Description of database
 *
 * @author Chris Vaughan
 */
class PlacesDatabase extends Database {

    //put your code here
    private $tables = ["errorlog", "areas", "places"];
    private $sql = ["CREATE TABLE `areas` (
  `code` char(2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `lastread` date DEFAULT '2000-01-01',
  `stats` blob NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
", "
CREATE TABLE `errorlog` (
`id` int(11) NOT NULL,
  `date` date NOT NULL,
  `errortext` varchar(256) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

", "

CREATE TABLE `places` (
`id` int(11) NOT NULL,
  `type` smallint(1) NOT NULL DEFAULT '0',
  `walkid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `gridref` text NOT NULL,
  `northing` int(11) NOT NULL,
  `easting` int(11) NOT NULL,
  `longitude` float NOT NULL DEFAULT '0',
  `latitude` float NOT NULL DEFAULT '0',
  `extras` blob NOT NULL,
  `score` smallint(6) NOT NULL DEFAULT '1',
  `dateused` date DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
", "
ALTER TABLE `areas`
 ADD PRIMARY KEY (`code`), ADD UNIQUE KEY `code` (`code`), ADD KEY `code_2` (`code`);
", "
ALTER TABLE `errorlog`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`);
", "
ALTER TABLE `places`
 ADD PRIMARY KEY (`id`);
", "
ALTER TABLE `errorlog`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
", "
ALTER TABLE `places`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;"];

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
                $ok = parent::insertRecord("areas", $names, $values);
                if ($ok) {
                    
                } else {
                    $this->addErrorLog(parent::error());
                }
            }
        } else {
            $this->addErrorLog(parent::error());
        }
        // load areas
    }

    function getNextArea() {
        $query = "SELECT * FROM areas ORDER BY lastread ASC LIMIT 1";
        $ok = parent::runQuery($query);
        $area = null;
        if ($ok) {
            while ($row = parent::getResult()->fetch_assoc()) {
                printf($row["lastread"] . " " . $row["code"] . $row["description"] . $row["lastread"]);
                $area = new RamblersOrganisationArea($row["code"], $row["description"]);
                $area->lastread = $row["lastread"];
            }
        } else {
            $this->addErrorLog(parent::error());
        }


        /* free result set */
        parent::freeResult();
        return $area;
    }

    function getAreaJson() {
        $query = "SELECT * FROM areas ORDER BY code";
        $ok = parent::runQuery($query);
        $areas = array();
        if ($ok) {
            while ($row = parent::getResult()->fetch_assoc()) {
                printf($row["lastread"] . " " . $row["code"] . " - " . $row["description"] . "<br />");
                $json = $row["stats"];
                if ($json != "") {
                    $areas[$row["code"] . " - " . $row["description"]] = json_decode($json);
                } else {
                    $areas[$row["code"] . " - " . $row["description"]] = NULL;
                }
            }
        } else {
            $this->addErrorLog(parent::error());
        }

        /* free result set */
        parent::freeResult();
        return $areas;
    }

    function updateAreaLastreadDate($area) {
        $query = "UPDATE `areas` SET `lastread`='[value-1]' WHERE code = '[value-2]'";
        $today = new DateTime("now");
        $todays = $today->format("Y-m-d");
        $query = str_replace("[value-1]", $todays, $query);
        $query = str_replace("[value-2]", $area->code, $query);
        $ok = parent::runQuery($query);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
    }

    function updateAreaStatistics($area, $json) {
        $query = "UPDATE `areas` SET `stats`='[value-1]' WHERE code = '[value-2]'";
        $query = str_replace("[value-1]", $json, $query);
        $query = str_replace("[value-2]", $area->code, $query);
        $ok = parent::runQuery($query);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
    }

    function addPlace($type, $walk, $point) {
        $id = $walk->id;
        // delete walk if already there
        $query = "DELETE FROM places WHERE walkid = " . $id;
        // insert new record
        $ok = parent::runQuery($query);
        if ($ok == false) {
            $this->addErrorLog(parent::error());
        }
        $extras = new PlacesExtras;
        $extras->group = $walk->groupCode;
        $extras->postcode = $point->postcode;
        $extras->latitude = $point->postcodeLatitude;
        $extras->longitude = $point->postcodeLongitude;

        $names = array();
        $names[] = "walkid";
        $names[] = "type";
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
        $ok = parent::insertRecord("places", $names, $values);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
    }

    function addReport($type, $gridref, $score, $description) {
        $query = "SELECT gridref,easting,northing,latitude,longitude,SUM(score) as total FROM places WHERE gridref = '[gridref]' GROUP BY gridref";
        $query = str_replace("[gridref]", $gridref, $query);
        $ok = parent::runQuery($query);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
        if ($ok == true) {
            $result = parent::getResult();
            /* fetch object array */
            while ($row = $result->fetch_row()) {
                //  printf("%s %s %s (%s)\r\n", $row[0], $row[1],$row[2],$row[3]);
                $gr = $row[0];
                $easting = $row[1];
                $northing = $row[2];
                $latitude = $row[3];
                $longitude = $row[4];
                $no = $row[5];
                $score = 1;

                $this->addExtraPlace($type, $gridref, $score, $description, $easting, $northing, $latitude, $longitude);
            }
        } else {
            echo "ERROR: Unable to read database record";
        }
        unset($result);
        parent::freeResult();
    }

    function addExtraPlace($type, $gridref, $score, $description, $easting, $northing, $latitude, $longitude) {
        if ($gridref == NULL) {
            $gridref = "";
        }
        if ($gridref == "") {
            echo "Rejected";
        } else {
            $names = array();
            $names[] = "type";
            $names[] = "name";
            $names[] = "score";
            $names[] = "gridref";
            $names[] = "easting";
            $names[] = "northing";
            $names[] = "longitude";
            $names[] = "latitude";
            $names[] = "dateused";
            $values = array();
            $values[] = $type;
            $values[] = $description;
            $values[] = $score;
            $values[] = $gridref;
            $values[] = $easting;
            $values[] = $northing;
            $values[] = $longitude;
            $values[] = $latitude;
            $today = new DateTime("now");
            $values[] = $today->format("Y-m-d");
            $ok = parent::insertRecord("places", $names, $values);
            if ($ok) {
                echo "Accepted: ";
            } else {
                $this->addErrorLog(parent::error());
            }
        }
    }

    function getPlaces($displayRejected) {
        $markers = "";
        $today = new DateTime("now");
        $todays = $today->format("Y-m-d");
        $query = "SELECT gridref,latitude,longitude,SUM(score) as total FROM places WHERE dateused <= '[todays]' GROUP BY gridref";
        $query = str_replace("[todays]", $todays, $query);
        $ok = parent::runQuery($query);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
        if ($ok == true) {
            $result = parent::getResult();
            /* fetch object array */
            while ($row = $result->fetch_row()) {
                //  printf("%s %s %s (%s)\r\n", $row[0], $row[1],$row[2],$row[3]);
                $gr = $row[0];
                $lat = $row[1];
                $long = $row[2];
                $no = $row[3];
                $icon = $this->getStarMarker($no);
                //  echo "var marker = L.marker([".$lat.", ".$long."]);";
                // echo " markerList.push(marker);\r\n";
                if ($icon != "") {
                    if ($displayRejected) {
                        If ($no < 0) {
                            $markers.= "addPlace(markerList ,\"" . $gr . "\", " . $no . ", " . $lat . ", " . $long . ", " . $icon . ");\r\n";
                        }
                    } else {
                        If ($no >= 0) {
                            $markers.= "addPlace(markerList ,\"" . $gr . "\", " . $no . ", " . $lat . ", " . $long . ", " . $icon . ");\r\n";
                        }
                    }
                }
            }
            unset($result);
            parent::freeResult();
            return $markers;
        }
    }

    private function getStarMarker($no) {

        $marker = "star0";

        if ($no >= 1) {
            $marker = "star1";
        }
        if ($no >= 2) {
            $marker = "star2";
        }
        if ($no >= 3) {
            $marker = "star3";
        }
        if ($no >= 4) {
            $marker = "star4";
        }
        if ($no >= 5) {
            $marker = "star5";
        }
        return $marker;
    }

    function getDetails($id) {
        $today = new DateTime("now");
        $todays = $today->format("Y-m-d");
        $query = "SELECT name,dateused FROM places WHERE gridref='" . $id . "' AND dateused <= '[todays]' ORDER BY dateused DESC";
        $query = str_replace("[todays]", $todays, $query);
        $ok = parent::runQuery($query);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
        if ($ok == true) {
            $result = parent::getResult();
            /* fetch object array */
            echo "<p><b>Description</b> [Date used]</p>";
            while ($row = $result->fetch_row()) {
                //  printf("%s %s %s (%s)\r\n", $row[0], $row[1],$row[2],$row[3]);
                $desc = $row[0];
                if ($desc == "") {
                    $desc = "[No description]";
                }
                $lastread = $row[1];
                echo  "  " . $desc ." <div class='small'>[". $lastread ."]</div><br/>";
            }
            unset($result);
            parent::freeResult();
        }
    }

    function addErrorLog($text) {
        $names = array();
        $names[] = "date";
        $names[] = "errortext";
        $values = array();
        $today = new DateTime("now");
        $values[] = $today->format("Y-m-d");
        $values[] = $text;

        echo "DB Error: " . $text;
        $ok = parent::insertRecord("errorlog", $names, $values);
        if ($ok) {
            echo "Accepted: ";
        } else {
            echo "Error: " . parent::error();
        }
    }

    function connect() {
        parent::connect();
        parent::createTables($this->sql);
    }

}
