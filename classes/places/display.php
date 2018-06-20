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
    private $agedate;
    private $compare;

    public function __construct($database, $stars, $age) {
        $this->db = $database;
        $this->stars = $stars;
        $this->age = $age;
        $this->compare = "newer";
        if ($this->age == "5older") {
            $this->compare = "older";
        }
        $this->agedate = PlacesFunctions::getAgeDate($this->age);
    }

    private function getCacheFileName() {
        $name = "cache/points";
        for ($index = 0; $index < count($this->stars); $index++) {
            if ($this->stars[$index]) {
                $name.="_star" . $index;
            }
        }
        $name.=$this->age;
        $name.=$this->compare;
        return $name . ".cache";
    }

    private function getPlaces() {
        $cachefile = $this->getCacheFileName();
        $lastupdated = PlacesFunctions::getDateFileLastUpdated($cachefile);
        $yesterday = new DateTime("yesterday");
        if ($lastupdated > $yesterday) {
            $points = file_get_contents($cachefile);
        } else {
            $points = $this->db->getPlaces($this->stars, $this->agedate, $this->compare);
            file_put_contents($cachefile, $points);
        }
        return $points;
    }

    public function display($menu) {
        $base = Baseaddr::get();
        $template = new Template("dist/mapTemplate.html");
        $template->replaceString("[[base]]", $base);
        $template->replaceString("<!--[[Insert menu]]--->", $menu->getMenu(Null));
        $template->replaceStringWithFile("<!--[[Insert Analytics]]--->", "analyticstracking.php");
        $points = $this->getPlaces();
        //  $points = $this->db->getPlaces($this->stars, $this->agedate, $this->compare);
        $template->replaceString("<!--[[Add markers here]]--->", "function addContent(ramblersMap) {" . $points . "}");
        $template->displayTemplate();
    }
}