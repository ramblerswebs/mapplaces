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
    public $json;

    //put your code here
    function __construct($area) {
        $feedurl = "http://www.ramblers.org.uk/api/lbs/walks?groups=".$area->code;
        $contents = file_get_contents($feedurl);
        if ($contents != "") {
            $this->json = json_decode($contents);
        }
    }

}
