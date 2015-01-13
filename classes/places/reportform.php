<?php

/**
 * Description of reportform
 *
 * @author Chris Vaughan
 */
class PlacesReportform {

    private $gridref;

    function __construct($gridref) {
        $this->gridref = $gridref;
    }

    function display() {
        //echo "report form";
        $template = new Template("dist/formTemplate.html");
        $template->insertTemplate();
    }

    function process() {
       
        echo "REPORT";
    }

}
