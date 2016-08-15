<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of menu
 *
 * @author Chris
 */
class PlacesMenu {

    Private $menuitems;

    public function __construct() {
        $this->menuitems = array();
    }

    Public function addItem($menuitem) {
        $this->menuitems[] = $menuitem;
    }

    public function getMenu($currentItem) {
        $out = "<div class='placesmenu'>";
        foreach ($this->menuitems as $item) {
            $out.= $item->getHtml($currentItem==$item);
        }
        return $out . "</div>";
    }

}