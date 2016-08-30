<?php

/**
 * Description of database
 *
 * @author Chris Vaughan
 */
class PlacesDatabase extends Database {

    const VALIDPERIOD = 5 * 365; // 5 years

    private $tables = ["errorlog", "groups", "places"];
    private $sql = ["CREATE TABLE `groups` (
  `code` text NOT NULL,
  `name` varchar(256) NOT NULL,
  `description` varchar(2560) NOT NULL,
  `scope` text NOT NULL,
  `url` varchar(500) NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL
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
ALTER TABLE `groups`
  ADD UNIQUE KEY `codeindex` (`code`(4));
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

    public function __construct($dbconfig) {
        parent::__construct($dbconfig);
    }

    public function storeGroups($groups) {
// clear area table
        $query = "TRUNCATE TABLE `groups`";
        $ok = parent::runQuery($query);
        if ($ok) {
            foreach ($groups as $group) {
                $names = $group->getNames();
                $values = $group->getValues();
                $ok = parent::insertRecord("groups", $names, $values);
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

    public function getGroups() {
        $query = "SELECT * FROM groups ORDER BY code";
        $ok = parent::runQuery($query);
        $groups = array();
        if ($ok) {
            while ($row = parent::getResult()->fetch_assoc()) {

                $group = new RamblersOrganisationGroup();
                $group->setCode($row["code"]);
                $group->setName($row["name"]);
                $group->setDescription($row["description"]);
                $group->setScope($row["scope"]);
                $group->setUrl($row["url"]);
                $group->setLatitude($row["latitude"]);
                $group->setLongitude($row["longitude"]);
                $groups[] = $group;
            }
        } else {
            $this->addErrorLog(parent::error());
        }


        /* free result set */
        parent::freeResult();
        return $groups;
    }

    public function addPlace($type, $walk, $point) {
        $id = $walk->id;
// delete walk/type if already there
        $query = "DELETE FROM places WHERE walkid = [id] AND type = [type]";
        $query = str_replace("[id]", $id, $query);
        $query = str_replace("[type]", $type, $query);
        $ok = parent::runQuery($query);
        if ($ok == false) {
            $this->addErrorLog(parent::error());
        }
// insert new record
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
        $names[] = "score";
        $gr = PlacesFunctions::checkGridRef($point->gridRef);
        $values = array();
        $values[] = $id;
        $values[] = $type;
        $values[] = $point->description;
        $values[] = $gr;
        $values[] = $point->easting;
        $values[] = $point->northing;
        $values[] = $point->longitude;
        $values[] = $point->latitude;
        $values[] = json_encode($extras);
        $values[] = $walk->date;
        $score = 1;
        if (strlen($gr) <> 8) {
            $score = -1;
        }
        $values[] = $score;
        $ok = parent::insertRecord("places", $names, $values);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
    }

    public function addReport($type, $gridref, $score, $description) {
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
                $this->addExtraPlace($type, $gridref, $score, $description, $easting, $northing, $latitude, $longitude);
            }
        } else {
            echo "ERROR: Unable to read database record";
        }
        unset($result);
        parent::freeResult();
    }

    public function addExtraPlace($type, $gridref, $score, $description, $easting, $northing, $latitude, $longitude) {
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

    public function getPlaces($stars, $agedate, $compare) {
        $markers = "";
        $query = "SELECT gridref,AVG(latitude),AVG(longitude),SUM(score*GREATEST(([Period]+DATEDIFF(dateused,CURDATE()))/[Period],0)) as total,MAX(dateused) FROM places WHERE dateused <= CURDATE() GROUP BY gridref";
        $query = str_replace("[Period]", self::VALIDPERIOD, $query);
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
                $lastused = $row[4];
                $which = intval($no + .5);
                $icon = PlacesFunctions::getStarMarker($which);
                If ($which < 0)
                    $which = 0;
                If ($which > 5)
                    $which = 5;
                $add = $stars[$which];
                $ageadd = false;
                if ($compare == "older") {
                    $ageadd = $lastused <= $agedate;
                }
                if ($compare == "newer") {
                    $ageadd = $lastused >= $agedate;
                }

                if ($add AND $ageadd) {
// echo "<br/>Date " . $lastused;
                    $markers.= "addPlace(mLst,\"" . $gr . "\"," . $which . "," . number_format($lat, 6, '.', '') . "," . number_format($long, 6, '.', '') . "," . $icon . ");\r\n";
                }
            }
            unset($result);
            parent::freeResult();
            return $markers;
        }
    }

    public function getDetails($id) {
        $today = new DateTime("now");
        $todays = $today->format("Y-m-d");
        $query = "SELECT name,dateused,score FROM places WHERE gridref='" . $id . "' AND dateused <= '[todays]' ORDER BY dateused DESC";
        $query = str_replace("[todays]", $todays, $query);
        $ok = parent::runQuery($query);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
        if ($ok == true) {
            $result = parent::getResult();
            /* fetch object array */
            echo "<p><b>Description</b> [Date used / Score]</p><ul>";
            $i = 0;
            while ($row = $result->fetch_row()) {             
                $desc = $row[0];
                $score = $row[2];
                $lastread = $row[1];
                $datetime1 = new DateTime();
                $datetime2 = new DateTime($lastread);
                $interval = $datetime1->diff($datetime2);
                $days = $interval->format("%a");
                $per = max((self::VALIDPERIOD - $days) / self::VALIDPERIOD, 0) * 100;
                $per = intval($per);
                $totscore = ceil($score * $per);
                if ($desc == "") {
                    $desc = "<span class='noDesc'>[No description]</span>";
                }

                $i+=1;
                if ($i > 10) {
                   // echo "<li><span class='small'>More . . .</span></li>";
                    break;
                }
                echo "<li><span class='small'>" . $desc . " [" . $lastread . " / " . $totscore . "%]</span></li>";
            }
            echo "</ul>";
            if ($i > 10) {
                    echo "<span class='small'><b>More . . .</b></span>";
                   
                }
            unset($result);
            parent::freeResult();
        }
    }

    public function removeOldLocationRecords() {
        $query = "DELETE FROM `places` WHERE `dateused`<DATE_SUB(NOW(), INTERVAL 10 YEAR)";
        $ok = parent::runQuery($query);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
    }

    public function removeMultipleLocations() {
        // find number of records for each location
        $query = "SELECT gridref,COUNT(*) FROM places WHERE dateused <= CURDATE() GROUP BY gridref";
        $ok = parent::runQuery($query);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
        if ($ok == true) {
            $result = parent::getResult();
            /* fetch object array */
            while ($row = $result->fetch_row()) {

                $gr = $row[0];
                $count = $row[1];

                if ($count > 20) {
                    echo "<p>$gr  $count</p>";
                    $this->removeLocation($gr, $count - 20);
                }
            }
        }
    }

    private function removeLocation($gr, $no) {
        $query = "DELETE FROM `places` WHERE `gridref`='[gridref]' AND dateused <= CURDATE() ORDER BY `dateused` LIMIT [limit] ";
        $query = str_replace("[gridref]", $gr, $query);
        $query = str_replace("[limit]", $no, $query);
        $ok = parent::runQuery($query);
        if (!$ok) {
            $this->addErrorLog(parent::error());
        }
    }

    public function addErrorLog($text) {
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

    public function connect() {
        parent::connect();
        parent::createTables($this->sql);
    }

    public function closeConnection() {
        parent::closeConnection();
    }

}
