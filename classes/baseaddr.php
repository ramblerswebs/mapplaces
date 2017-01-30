<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of baseaddr
 *
 * @author Chris Vaughan
 */
class Baseaddr {

    private static $baseaddr;

    public static function set() {
        $base = $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
      //  echo $base;
        $pieces = explode("/", $base);
        $pieces[count($pieces) - 1] = "";
      //  var_dump($pieces);
        self::$baseaddr = implode("/", $pieces);
       // echo self::$baseaddr;
    }

    public static function get() {
        return self::$baseaddr;
    }

}
