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
    private $displayRejected;

    function __construct($database,$displayRejected) {
        $this->db = $database;
        $this->displayRejected=$displayRejected;
    }

    function display() {
        $template = new Template("dist/mapTemplate.html");
        $points = $this->db->getPlaces($this->displayRejected);
        $template->replaceString("// [[Add markers here]]", $points);
        $template->insertTemplate();
    }

}
