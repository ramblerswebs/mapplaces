<?php

class PlacesRecord {

    public $GR;
    public $Lat;
    public $Lng;
    public $S;
    public $D;

    public function __construct($gr, $lat, $lng, $stars) {
        $this->GR = $gr;
        $this->Lat = number_format($lat, 6, '.', '');
        $this->Lng = number_format($lng, 6, '.', '');
        $this->S = $stars;
    }

}
