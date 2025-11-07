<?php

class Config {

    public $database;

    function __construct() {
        $this->database = new Dbconfig("localhost", "places", "root", "root");
    }

}
