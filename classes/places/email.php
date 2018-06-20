<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of email
 *
 * @author Chris
 */
class PlacesEmail {

    public static function send($subtitle, $text) {
        $email = "admin@walkinginfo.co.uk";
        $headers = "From: admin@walkinginfo.co.uk\r\n";
        $headers .= "Content-type: text/html\r\n";
        $title = "[Starting Places] " . $subtitle;
        $mailed = mail($email, $title, $text, $headers);
    }

}
