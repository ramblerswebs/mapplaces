<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of importcsv
 *
 * @author Chris Vaughan
 */
class PlacesImportcsv {

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    function process($file) {
        $file_handle = fopen($file, "r");

        while (!feof($file_handle)) {

            $items = fgetcsv($file_handle, 1024);

            $this->processRecord($items);
            print $items[0] . " Gridref: " . $items[1] . "<BR>";
        }

        fclose($file_handle);
    }

    private function processRecord($items) {
        $type = PlacesEnums::FromCSVFile;
        $gridref = $items[1];
        $description = $items[0];
        $description = str_replace($gridref, "", $description);
        $description = trim($description);
        $score = 1;
        $easting = $items[2];
        $northing = $items[3];
        $longitude = $items[4];
        $latitude = $items[5];

        $result = $this->db->addExtraPlace($type, $gridref, $score, $description, $easting, $northing, $latitude, $longitude);
        echo "Accepted";
    }

}
