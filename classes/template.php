<?php

/**
 * Description of inserthtml
 *
 * @author Chris Vaughan
 */
class Template {

    private $contents;

    public function __construct($template) {
        $this->contents = file_get_contents($template);
    }

    public function replaceString($string, $with) {
        $this->contents = str_replace($string, $with, $this->contents);
    }

    public function replaceStringWithFile($string, $file) {
        $with = file_get_contents($file);
        $this->contents = str_replace($string, $with, $this->contents);
    }

    public function displayTemplate() {
        echo $this->contents;
    }

}
