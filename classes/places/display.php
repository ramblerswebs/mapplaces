<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of display
 *
 * @author Chris Vaughan
 */
class PlacesDisplay {

    private $db;

    function __construct($database) {
        $this->db = $database;
    }

    function display() {
        $template = new Template("dist/mapTemplate.html");
        $points = $this->db->getPlaces();
        $template->replaceString("// [[Add markers here]]", $points);
        $template->insertTemplate();
    }

}
