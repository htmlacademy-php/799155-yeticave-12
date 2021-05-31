<?php
require_once('helpers.php');

class Database {
    private $connection;
    private $error;

    protected function setError(string $error) {
      $this->error = $error;
    }
    
    public function __construct()
    {
      $this->error = null;
      $this->connection = mysqli_connect("localhost", "root", "", "yeticave");
      if ($this->connection == false) {
          $this->error = mysqli_connect_error();
      } else {
          mysqli_set_charset($this->connection, "utf8");
      }
    }

    public function __destruct() {
      mysqli_close($this->connection);
    }

    public function isOk() : bool {
      return $this->error == null;
    }

    public function getError() {
      return $this->error;
    }

    public function query($sql) {
      if ($this->connection) {
        $this->error = null;
        $result = mysqli_query($this->connection, $sql);
        if ($result == false) {
            $this->error = mysqli_error($this->connection);
        }
        return $result;
      } else {
        return false;
      }
    }

    public function prepare($sql, $data = []) {
      if ($this->connection) {
        $this->error = null;
        $stmt = db_get_prepare_stmt($this->connection, $sql, $data);
        return $stmt;
      } else {
        return false;
      }
    }

    public function getEscapeStr(string $str) : string {
      if (get_magic_quotes_gpc()) {
        $str  =  stripslashes($str);
      } 
      if ($this->connection) {
        return mysqli_real_escape_string($this->connection, $str);
      }
      return $str;
    }   
    
    public function getLastId() {
      if ($this->isOk()) {
        return mysqli_insert_id($this->connection);
      }
      return false;
    }        
}
