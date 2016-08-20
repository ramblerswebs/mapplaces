<?php

/**
 * Description of control
 *
 * @author Chris
 */
class PlacesControl {

    private $mLastAreaProcessed = "fred";
    private $filename = "notset";
    const controlfilename="control/control.json";

    public function __construct() {
        $this->filename = getcwd() . self::controlfilename;
        if (file_exists($this->filename)) {
            $storage = file_get_contents($this->filename);
            if ($storage != "") {
                $json = json_decode($storage);
                //echo "area : " . $json->lastAreaProcessed;
                $this->mLastAreaProcessed = $json->lastAreaProcessed;
            }
        }
    }

    public function lastAreaProcessed() {
        return $this->mLastAreaProcessed;
    }

    public function updateLastAreaProcessed($area) {
        $this->mLastAreaProcessed = $area->getCode();
        $json = '{"lastAreaProcessed":"' . $area->getCode(). '"}';
        file_put_contents($this->filename, $json);
    }

    public static function getDateAreaLastUpdated($areacode) {
        // find date area log file was last updated
        $logfile = "logfiles/logfile_area_" . $areacode . ".log";
        return PlacesFunctions::getDateFileLastUpdated($logfile);
        if (file_exists($logfile)) {
            $timestamp= filemtime($logfile);
            $lastrun=new DateTime();
            $lastrun=$lastrun->setTimestamp ($timestamp);
            return $lastrun;
        } else {
            return new DateTime('2000-01-01');
        }
    }

}
