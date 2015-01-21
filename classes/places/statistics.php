<?php

/**
 * Description of statistics
 *
 * @author Chris Vaughan
 */
class PlacesStatistics {

    private $db;

    function __construct($database) {
        $this->db = $database;
    }

    function display() {
        $areas = $this->db->getAreaJson();

        foreach ($areas as $key => $groups) {
            echo "<h2>" . $key . "</h2>";
            if ($groups == NULL) {
                echo "<ul>";
                echo "<li>No walks defined</li>";
                echo "</ul>";
            } else {
                foreach ($groups as $group) {
                    echo $group->groupcode . " - " . $group->groupname . "<br />";
                    $types = $group->types;
                    echo "<ul>";
                    foreach ($types as $tkey => $type) {
                        echo "<li>" . $tkey . ": " . $type . "</li>";
                    }
                    echo "</ul>";
                }
            }
        }
    }

}
