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
    private $groupsblankcontact;

    function __construct($db, $area, $walks) {
        $this->db = $db;
        $this->area = $area;
        $this->walks = $walks;
        $this->groups = array();
        $this->groupsblankcontact = array();
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
                if ($walk->walkContact != null) {
                                $contactName = $walk->walkContact->contact->displayName;
                           } else {
                               
                           }
                
                if ($contactName == "") {
                    if (!isset($this->groupsblankcontact[$groupcode])) {
                        $this->groupsblankcontact[$groupcode] = "";
                    }
                    $this->groupsblankcontact[$groupcode] .= $walk->id . ", ";
                }
            }
            $json = json_encode($this->groups, JSON_PRETTY_PRINT);
            Logfile::write($json);
            logfile::write("Walks with blank contact display name");
            $json = json_encode($this->groupsblankcontact, JSON_PRETTY_PRINT);
            Logfile::write($json);
            $this->db->updateAreaStatistics($this->area, $json);
            echo $json;
            Logfile::write(" ");
        }
    }

}
