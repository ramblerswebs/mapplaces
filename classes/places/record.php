<?php


class PlacesRecord {

    public $Lat;
    public $Lng;
    public $S;
    public $Distance;

    public function __construct($lat, $lng, $stars) {
        $this->Lat = number_format($lat, 6, '.', '');
        $this->Lng = number_format($lng, 6, '.', '');
        $this->S = $stars;
    }

}
