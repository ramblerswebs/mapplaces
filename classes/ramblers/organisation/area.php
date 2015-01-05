<?php

/**
 * Description of area
 *
 * @author Chris Vaughan
 */
class RamblersOrganisationArea {

    public $code;
    public $description;
    public $lastread;

    function __construct($code, $description) {
        $this->code = $code;
        $this->description = $description;
        $this->lastread='2000-01-01';
    }

    function getNames() {
        $out = array();
        $out[] = "code";
        $out[] = "description";
        $out[] = "lastread";
        return $out;
    }

    function getValues() {
         $out = array();
        $out[] = $this->code;
        $out[] = $this->description;
        $out[] = $this->lastread;
        return $out;
    }

}
