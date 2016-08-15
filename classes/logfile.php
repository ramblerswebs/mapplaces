<?php

/**
 * Description of logfile
 *
 * @author Chris Vaughan
 */
class Logfile {

    private static $logfile;

    public static function create($name) {
        self::$logfile = fopen($name, "w") or die("Unable to open logfile file!");
    }

    public static function write($text) {
        if (isset(self::$logfile)) {
            fwrite(self::$logfile, $text . "\n");
        }
    }

    public static function writeWhen($text) {
        $today = new DateTime(NULL);
        $when = $today->format('Y-m-d H:i:s');
        self::write($when . ": " . $text);
    }

    public static function writeError($text) {
        self::writeWhen(" ERROR: " . $text);
    }

    public static function close() {
        if (isset(self::$logfile)) {
            fclose(self::$logfile);
        }
    }

}
