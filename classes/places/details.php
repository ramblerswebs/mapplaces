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
        $image = PlacesFunctions::getStarsImageUrl($no);
        echo "<img width=\"100\" height=\"20\" alt=\"stars\" src=\"" . $image . "\">";

        echo "<p>";
        echo "Place Grid Ref: " . $id;
        echo "<span id='reportphotos'><a href=\"javascript:photos('" . $id . "') \">Photos of area</a></span>";
        echo "<span id='reportmap'><a href=\"javascript:streetmap('" . $id . "') \">Streetmap</a></span>";
        echo "</p>";
        echo "<div id='reportform'>";

        echo "</div>";

        $this->db->getDetails($id);
        echo "<hr/>";
        // echo "<p>Report incorrect place/location<br/>";
        // echo "<span id='reportdesc'><a href=\"javascript:reportDescription('" . $id . "') \">Provide better Description</a></span>";
        echo "<span id='reportgr'><a href=\"javascript:reportGridref('" . $id . "') \">Report incorrect location/grid reference</a></span>";
        echo "</p>";
        echo "<div id='placereport'></div>";
    }

}
