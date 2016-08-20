<?php

/**
 * Description of areas
 *
 * @author Chris Vaughan
 */
class RamblersOrganisationGroups {

    private $mgroups;
    private $db;

    const ORGFEED = "feed/organisation.json";

    public function __construct($db) {
        $this->db = $db;
        $this->updateGroupsFromFeed();
        $this->mgroups = $db->getGroups();
        if (count($this->mgroups) == 0) {
            $this->updateGroupCodes();
        }
    }

    private function updateGroupsFromFeed() {
        $lastdate = $this->getGroupsFeedDate();
        $int = new DateInterval("P26D");
        $updatedate = new DateTime("Now");
        $updatedate->sub($int);
        if ($updatedate > $lastdate) {
            // Update organisation
            $this->updateGroupCodes();
        }
    }

    private function getGroupsFeedDate() {
        $lastrun = new DateTime("2000/01/01");
        if (file_exists(self::ORGFEED)) {
            $timestamp = filemtime(self::ORGFEED);
            $lastrun = new DateTime();
            $lastrun = $lastrun->setTimestamp($timestamp);
        }
        return $lastrun;
    }

    public function updateGroupCodes() {
        $this->readGroupCodesFeed();
        foreach ($this->mgroups as $group) {
            $group->listGroup();
        }
        if (count($this->mgroups) > 0) {
            $this->db->storeGroups($this->mgroups);
            PlacesEmail::send("Successful", "Ramblers Organisation has been updated");
        } else {
            PlacesEmail::send("Error", "Update of Ramblers Organisation failed");
        }
    }

    private function readGroupCodesFeed() {
        $url = "http://www.ramblers.org.uk/api/lbs/groups/";

        // Get the JSON information
        $groupsjson = file_get_contents($url);
        $groups = [];
        $this->mgroups = array();
        if ($groupsjson != "") {
            $groups = json_decode($groupsjson);
            file_put_contents(self::ORGFEED, $groupsjson);
            unset($groupsjson);
            if (json_last_error() <> JSON_ERROR_NONE) {

                echo '<br/><b>Groups feed: feed is not in Json format</b>';
            }
            foreach ($groups as $value) {
                $group = new RamblersOrganisationGroup();
                $group->setCode($value->groupCode);
                $group->setName($value->name);
                $group->setDescription($value->description);
                $group->setScope($value->scope);
                $group->setUrl($value->url);
                $group->setLatitude($value->latitude);
                $group->setLongitude($value->longitude);
                $this->mgroups[$value->groupCode] = $group;
            }
        }
    }

    //  public function Groups() {
    //      return $this->mgroups;
    //  }

    public function nextArea($lastAreaProcessed) {
        $first = null;
        foreach ($this->mgroups as $group) {
            If ($group->getScope() == "A") {
                if ($first == null) {
                    $first = $group;
                }
                if ($group->getCode() > $lastAreaProcessed) {
                    return $group;
                }
            }
        }
        return $first;
    }

}
