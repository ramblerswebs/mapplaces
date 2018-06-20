<?php

/**
 * Description of area
 *
 * @author Chris Vaughan
 */
class RamblersOrganisationGroup {

    private $code;
    private $description;
    private $name;
    private $scope;
    private $url;
    private $latitude;
    private $longitude;

    public function __construct() {
        $this->code = "";
        $this->description =  "";
        $this->name =  "";
        $this->scope = "";
        $this->url = "";
        $this->latitude = 0;
        $this->longitude = 0;
    }

    public function listGroup() {
        switch ($this->scope) {
            case "A":
                echo "Area: " . $this->name . " (" . $this->code . ")".PHP_EOL;
                break;
            case "G":
                echo "\tGroup: " . $this->name . " (" . $this->code . ")".PHP_EOL;
                break;
            case "S":
                echo "\tSpecial Group: " . $this->name . " (" . $this->code . ")".PHP_EOL;
                break;

            default:
                echo "Unknown Group type: " . $this->name . " (" . $this->code . ")".PHP_EOL;
                break;
        }
    }

    public function code() {
        return $this->code;
    }

    public function getNames() {
        $out = array();
        $out[] = "code";
        $out[] = "name";
        $out[] = "description";
        $out[] = "scope";
        $out[] = "url";
        $out[] = "latitude";
        $out[] = "longitude";
        return $out;
    }

    public function getValues() {
        $out = array();
        $out[] = $this->code;
        $out[] = $this->name;
        $out[] = $this->description;
        $out[] = $this->scope;
        $out[] = $this->url;
        $out[] = $this->latitude;
        $out[] = $this->longitude;
        return $out;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($value) {
        $this->code = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($value) {
        $this->description = $value;
    }

    public function getScope() {
        return $this->scope;
    }

    public function setScope($value) {
        $this->scope = $value;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($value) {
        $this->url = $value;
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function setLatitude($value) {
        $this->latitude = $value;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function setLongitude($value) {
        $this->longitude = $value;
    }

}
