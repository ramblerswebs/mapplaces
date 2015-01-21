<?php

/**
 * Description of display
 *
 * @author Chris Vaughan
 */
class PlacesDetails {

    private $db;

    function __construct($database) {
        $this->db = $database;
    }

    function display($id, $no) {
        echo "<div id='reportoptions'>";
        echo "<div id='reportdesc'><a href=\"javascript:reportDescription('" . $id . "') \">Update Description</a></div>";
        echo "<div id='reportgr'><a href=\"javascript:reportGridref('" . $id . "') \">Incorrect Grid Ref</a></div>";
        echo "</div>";
        echo "<div id='reportphotos'><a href=\"javascript:photos('" . $id . "') \">Photos/OS Map</a></div>";
        $image = $this->getStarsImage($no);
        if ($image != null) {
            echo "<img width=\"100\" height=\"20\" alt=\"stars\" src=\"" . $image . "\">";
        }
        echo "<p>";
        echo "Place Grid Ref: " . $id . " </p>";
        echo "<div id='reportform'>";

        echo "</div>";
        echo "<div id='placereport'></div>";
        $this->db->getDetails($id);
    }

    function getStarsImage($no) {

        switch ($no) {

            case 1:
                return "images/1_stars.png";
                break;
            case 2:
                return "images/2_stars.png";
                break;
            case 3:
                return "images/3_stars.png";
                break;
            case 4:
                return "images/4_stars.png";
                break;
            case 5:
                return "images/5_stars.png";
                break;
        }
        return null;
    }

}
