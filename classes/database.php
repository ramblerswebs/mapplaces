<?php

// Database class allows you to
//   Connect to a MySql database
//   Close the connection

class Database {

    private $status;
    private $connected;
    private $config;
    private $mysqli;
    private $result;

    public function __construct($dbconfig) {
        $this->config = $dbconfig;
        $this->status = "Disconnected";
    }

    public function connect() {
        // echo 'Connecting' . "\n";
        $this->connected = False;
        $host = $this->config->host;
        $database = $this->config->database;
        $user = $this->config->user;
        $password = $this->config->password;
        // echo '<p>Host - ' . $host . "</p>\n";
        // echo '<p>Database - ' . $database . "</p>\n";
        //  echo '<p>User - ' . $user . "</p>\n";
        //  echo '<p>Password - ' . $password . "</p>\n";
        $this->mysqli = new mysqli($host, $user, $password, $database);
        if ($this->mysqli->connect_error) {
            $this->status = 'Error connecting to database (' . $this->mysqli->connect_errno . ') '
                    . $this->mysqli->connect_error;

            $this->connected = False;
            return;
        } else {
            $this->status = "Connected";
            $this->connected = True;
            return;
        }
    }

    public function connected() {
        return $this->connected;
    }

    public function error() {
        return $this->mysqli->error;
    }

    public function createTables($sql) {
        $res = $this->mysqli->Query("SHOW TABLES");
        if ($res->num_rows == 0) {
            echo "Creating tables<br/>";
            foreach ($sql as $value) {
                echo $value . "<br/>";
                $res = $this->mysqli->Query($value);
                if ($res) {
                    //$this->Msg("Table 'baseline' created");
                } else {
                    echo "SQL failed " . $value . " Error: " . $this->mysqli->error;
                }
            }
        }
    }

    public function runQuery($query) {
        $this->result = $this->mysqli->Query($query);
        if ($this->result) {
            return true;
        } else {
            Logfile::writeError($this->mysqli->error);
            return false;
        }
    }

    public function getResult() {
        return $this->result;
    }

    public function freeResult() {
        $this->result->close();
        unset($this->result);
    }

    public function insertRecord($table, $names, $values) {
        $query = "Insert into " . $table;
        $query.=self::createNames($names);
        $query.=self::createValues($values);
        $result = $this->mysqli->query($query);
        if ($result === false) {
            return false;
        }
        return true; // we're done for this file
    }

    private function tableExists($table) {
        $res = $this->mysqli->Query("SHOW TABLES LIKE '" . $table . "'");
        return $res->num_rows > 0;
    }

    public function closeConnection() {
        $this->mysqli->close();
        $this->status = "Disconnected";
        $this->connected = False;
    }

    private static function createNames($names) {
        // $query = "(filepath, hash, state, date_added, date_checked)";
        $out = " (";
        foreach ($names as $name) {
            $out.=$name;
            $out.=", ";
        }
        $out = substr($out, 0, -2) . ") ";
        return $out;
    }

    private static function createValues($values) {
        // $query = "values ('" . addslashes($filepath) . "', '$hash', " . self::STATE_NEW . ", NOW(), 0)";
        $out = " values ('";
        foreach ($values as $value) {
            $out.=addslashes($value);
            $out.="', '";
        }
        $out = substr($out, 0, -3) . ") ";
        return $out;
    }

}
