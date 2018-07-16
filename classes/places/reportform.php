<?php

/**
 * Description of reportform
 *
 * @author Chris Vaughan
 */
class PlacesReportform {

    private $gridref;
    private $reporttype;
    private $db;

    const eDesc = "description";
    const eGR = "gridref";

    function __construct($db, $gridref, $reporttype) {
        $this->db = $db;
        $this->gridref = $gridref;
        $this->reporttype = $reporttype;
    }

    function display() {
//echo "report form";
        $template = new Template("dist/formTemplate.html");
        $template->replaceString("[typeValue]", $this->reporttype);
        $template->replaceString("[grValue]", $this->gridref);
// if ($this->reporttype == PlacesReportform::eDesc) {
//     $template->replaceString("[title]", "New Description");
//    $template->replaceString("[textValue]", "");
// } else {
        $template->replaceString("[title]", "Confirm location/grid reference is not correct  ");
        $template->replaceString("[textValue]", "Incorrect grid ref");
// }
        $template->displayTemplate();
    }

    function process($description) {
        $desc = strip_tags($description);
        if ($this->reporttype == PlacesReportform::eDesc) {
            $score = 1;
        } else {
            $score = -1; // gridref
        }
        $type = PlacesEnums::FromUserReport;
        $ok = $this->db->addReport($type, $this->gridref, $score, $desc);
        if ($ok) {
            echo "Accepted";
        } else {
            echo "ERROR: Unable to read database record";
        }
    }

    function reportButton() {
        $score = '';
        if ($this->reporttype == "like") {
            $desc = "User like " . PlacesFunctions::getUserIP();
            $score = 1;
        }
        if ($this->reporttype == "dislike") {
            $desc = "User dislike " . PlacesFunctions::getUserIP();
            $score = -1; // gridref
        }
        If ($score !== 0) {
            $type = PlacesEnums::FromUserReport;
            $ok = $this->db->addReport($type, $this->gridref, $score, $desc);
            return $ok;
        }
    }

}
