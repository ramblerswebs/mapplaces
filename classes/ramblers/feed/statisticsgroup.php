<?php

/**
 * Description of Statisticsgroup
 *
 * @author Chris Vaughan
 */
class RamblersFeedStatisticsgroup {

    public $walkids;
    public $types;
    public $groupcode;
    public $groupname;

    const exact = "=Exact, ";
    const nonexact = "=Non-Exact, ";
    

    function __construct($groupcode, $groupname) {
        $this->groupcode = $groupcode;
        $this->groupname = $groupname;
        $this->types = array();
        $this->walkids = array();
    }

    function addWalk($walk) {
        $type = $this->processPoints($walk);
       // echo $type."<br/>";
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
    
      function processPoints($walk) {
        $points = $walk->points;
        $type = "";
        foreach ($points as $point) {
            $type.=$point->typeString;
            if ($point->showExact == true) {
                $type.=RamblersFeedStatisticsgroup::exact;
            } else {
                $type.=RamblersFeedStatisticsgroup::nonexact;
            }
        }
        return $type;
    }

}
