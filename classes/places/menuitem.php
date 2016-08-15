<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of menuitem
 *
 * @author Chris
 */
class PlacesMenuitem {

    private $name;
    private $link;

    public function __construct($name, $link) {
        $this->name = $name;
        $this->link = $link;
    }

    Public function getHtml($current) {
        $class = "link-button-small button-p7474";
        $classcurrent = "link-button-small button-grey";
        if ($current){
             $out = "<span class='" . $classcurrent . "'>" . $this->name . "</span>";
        } else {
              $out = "<span class='" . $class . "'><a href='" . $this->link . "'>" . $this->name . "</a></span>";
        }   
        return $out;
    }

}
