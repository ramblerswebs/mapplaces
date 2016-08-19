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

    function display($no, $gr, $lat, $long) {
        $image = PlacesFunctions::getStarsImageUrl($no);
        echo "<img width=\"100\" height=\"20\" alt=\"stars\" src=\"" . $image . "\">";

        echo "<p>";
        echo "Place Grid Ref: " . $gr;
        echo "<span class='reportbutton-green'><a href=\"javascript:photos('" . $gr . "') \">Photos of area</a></span>";
        echo "<span class='reportbutton-green'><a href=\"javascript:streetmap('" . $gr . "') \">Streetmap</a></span>";
        echo "<span class='reportbutton-green'><a href=\"javascript:googlemap(" . $lat . "," . $long . ") \">Google Map</a></span>";
        echo "<p class='small'>Lat/Long: " . $lat . ", " . $long . "</p>";
        echo "</p>";
        echo "<div id='reportform'>";

        echo "</div>";

        $this->db->getDetails($gr);
        echo "<hr/>";
        // echo "<p>Report incorrect place/location<br/>";
        // echo "<span id='reportdesc'><a href=\"javascript:reportDescription('" . $id . "') \">Provide better Description</a></span>";
        echo "<span class='reportbutton-red'><a href=\"javascript:reportGridref('" . $gr . "') \">Report incorrect location/grid reference</a></span>";
        echo "</p>";
        echo "<div id='placereport'></div>";
    }

}
