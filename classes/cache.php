<?php

/*
 * 
 * 
 */

/**
 * Description of cache
 *
 * @author ChrisV
 */
class Cache {

    private $cacheFolder = "cache/";
    private $filename = null;

    public function __construct($name) {
        $this->filename = $this->cacheFolder . $name;
    }

    public function getCachedString() {
        if (file_exists($this->filename)) {
            $result = file_get_contents($this->filename);
            if ($result === false) {
                return null;
            } else {
                return $result;
            }
        }
        return null;
    }

    public function saveString($data) {
        $result = file_put_contents($this->filename, $data);
        if ($result === false) {
            PlacesEmail::send('Unable to store Get All cache file', '');
        }
    }

    public function deleteCachedString() {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

}
