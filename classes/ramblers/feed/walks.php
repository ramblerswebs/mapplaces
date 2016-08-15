<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of walks
 *
 * @author Chris Vaughan
 */
class RamblersFeedWalks {

    private $json;
    private $error = 0;

    //put your code here
    public function __construct($areacode) {
        $feedurl = "http://www.ramblers.org.uk/api/lbs/walks?groups=" . $areacode;
        $contents = file_get_contents($feedurl);
        if ($contents != "") {
            $this->json = json_decode($contents);
            file_put_contents("feed/area_" . $areacode . ".json", $contents);
        } else {
            $this->error = 1;
        }
    }

    public function getJson() {
        return $this->json;
    }

    public function errorCode() {
        return $this->error;
    }

}
