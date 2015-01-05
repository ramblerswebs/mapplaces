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

    function display($id) {

        echo "<p>Place Grid Ref: " . $id . "       <a href=\"javascript:photos('".$id."') \">Photos of area</a></p>";
        $this->db->getDetails($id);
        
    }

}
