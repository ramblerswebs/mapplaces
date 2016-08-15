<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of functions
 *
 * @author Chris
 */
class PlacesFunctions {

    //put your code here

    public static function getStarMarker($no) {

        $marker = "s0";

        if ($no >= 1) {
            $marker = "s1";
        }
        if ($no >= 2) {
            $marker = "s2";
        }
        if ($no >= 3) {
            $marker = "s3";
        }
        if ($no >= 4) {
            $marker = "s4";
        }
        if ($no >= 5) {
            $marker = "s5";
        }
        return $marker;
    }

    public static function checkGridRef($gr) {
        $len = strlen($gr);
        switch ($len) {
            case 8:
                return $gr;
                break;
            case 10:
                $out = substr($gr, 0, 5) . substr($gr, 6, 3);
                return $out;
                break;
            case 12:
                $out = substr($gr, 0, 5) . substr($gr, 7, 3);
                return $out;
                break;
            default:
                return $gr;
                break;
        }
    }

    public static function getDateFileLastUpdated($filename) {
        // find date file was last updated
        if (file_exists($filename)) {
            $timestamp = filemtime($filename);
            $lastupdated = new DateTime();
            $lastupdated = $lastupdated->setTimestamp($timestamp);
            return $lastupdated;
        } else {
            return new DateTime('2000-01-01');
        }
    }

    public static function getStarsImageUrl($no) {
        if ($no < 0) {
            $no = 0;
        }
        if ($no > 5) {
            $no = 5;
        }
        switch ($no) {
            case 0:
                return "images/0_stars.png";
                break;
            case 1:
                return "images/1_stars.png";
                break;
            case 2:
                return "images/2_stars.png";
                break;
            case 3:
                return "images/3_stars.png";
                break;
            case 4:
                return "images/4_stars.png";
                break;
            case 5:
                return "images/5_stars.png";
                break;
            default:
                return "images/0_stars.png";
                break;
        }
        return null;
    }

    public static function getAgeDate($age) {
        $today = new DateTime("now");
        $interval = 'P0Y';
        switch ($age) {
            case "10years":
                $interval = 'P10Y';
                break;
            case "5years":
                $interval = 'P5Y';
                break;
            case "3years":
                $interval = 'P3Y';
                break;
            case "1years":
                $interval = 'P1Y';
                break;
            case "10older":
                $interval = 'P10Y';
                break;
        }
        $int = new DateInterval($interval);
        $agedate = $today->sub($int);
        return $agedate->format("Y-m-d");
    }

}
