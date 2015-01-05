<?php

/**
 * Output statistics for walks feed
 *
 * @author Chris Vaughan
 */
class RamblersFeedStatistics {

    private $walks;
    private $walkids;
    private $types;
    private $exact = " Exact  ";
    private $nonexact = " Non-Exact  ";

    function __construct($walks) {
        $this->walks = $walks;
        $this->types = array();
        $this->walkids = array();
    }

    function Display() {
        if ($this->walks != NULL) {
            foreach ($this->walks as $walk) {
                $type = $this->processPoints($walk);
                if (isset($this->types[$type])) {
                    $this->types[$type]+=1;
                } else {
                    $this->types[$type] = 1;
                } 
                if (isset($this->walkids[$type])) {
                    $this->walkids[$type].=$walk->id . ", ";
                } else {
                    $this->walkids[$type] = $walk->id . ", ";
                }
            }
            Logfile::write(" ");
            Logfile::write("Walk types");
            Logfile::write(" ");
            foreach ($this->types as $key => $value) {
                Logfile::write("    " . str_pad($key, 45) . ": " . $value);
            }
            Logfile::write(" ");
            Logfile::write("Walk types ids");
            Logfile::write(" ");
            foreach ($this->walkids as $key => $value) {
                Logfile::write("    " . str_pad($key, 45) . ": " . $value);
            }
            Logfile::write(" ");
        }
    }

    function processPoints($walk) {
        $points = $walk->points;
        $type = "";
        foreach ($points as $point) {
            $type.=$point->typeString;
            if ($point->showExact == true) {
                $type.=$this->exact;
            } else {
                $type.=$this->nonexact;
            }
        }
        return $type;
    }

}
