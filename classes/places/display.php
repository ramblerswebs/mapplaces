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
    private $stars;  // array defining which starred walks should be displayed
    private $age;

    public function __construct($database, $stars, $age) {
        $this->db = $database;
        $this->stars = $stars;
        $this->age = $age;
    }

    public function display($menu) {
        $template = new Template("dist/mapTemplate.html");
        $template->replaceString("// [[Insert menu]]", $menu->getMenu(Null));
        $compare = "newer";
        if ($this->age == "10older") {
            $compare = "older";
        }

        $agedate = PlacesFunctions::getAgeDate($this->age);
        $points = $this->db->getPlaces($this->stars, $agedate, $compare);
        $template->replaceString("// [[Add markers here]]", $points);
        $template->insertTemplate();
    }

}
