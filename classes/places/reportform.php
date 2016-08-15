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
        $template->insertTemplate();
    }

    function process($description) {
        $desc = strip_tags($description);
        if ($this->reporttype == PlacesReportform::eDesc) {
            $score = 1;
        } else {
            $score = -1; // gridref
        }
        $type = PlacesEnums::FromUserReport;
        $this->db->addReport($type, $this->gridref, $score, $desc);
    }

}
