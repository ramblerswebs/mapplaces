<?php

/**
 * Output statistics for walks feed
 *
 * @author Chris Vaughan
 */
class RamblersFeedStatistics {

    private $db;
    private $area;
    private $walks;
    private $groups;

    function __construct($db, $area, $walks) {
        $this->db = $db;
        $this->area = $area;
        $this->walks = $walks;
        $this->groups = array();
    }

    function Display() {
        if ($this->walks != NULL) {
            foreach ($this->walks as $walk) {
                $groupcode = $walk->groupCode;
                $groupname = $walk->groupName;
                if (!isset($this->groups[$groupcode])) {
                    $this->groups[$groupcode] = new RamblersFeedStatisticsgroup($groupcode, $groupname);
                }
                $this->groups[$groupcode]->addWalk($walk);
            }
            $json = json_encode($this->groups, JSON_PRETTY_PRINT);
            Logfile::write($json);
            $this->db->updateAreaStatistics($this->area, $json);
            echo $json;
            Logfile::write(" ");
        }
    }

}
