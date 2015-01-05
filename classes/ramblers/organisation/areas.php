<?php

/**
 * Description of areas
 *
 * @author Chris Vaughan
 */
class RamblersOrganisationAreas {

    //put your code here


    function readAreaCodes($file) {
        $out = null;
        if (file_exists($file)) {
            $xml = simplexml_load_file($file);
            $out = array();
            foreach ($xml->children() as $child) {
                $code = $this->xml_attribute($child, "value");
                $desc = (string) $child;
                $area = new RamblersOrganisationArea($code, $desc);
                $out[] = $area;
            }
        } else {
            echo "Unable to find file: " . $file;
        }
        return $out;
    }

    function xml_attribute($object, $attribute) {
        if (isset($object[$attribute])) {
            return (string) $object[$attribute];
        }
    }

}
