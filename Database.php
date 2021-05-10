<?php
class Database {
    private $connection;
    private $error;

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

    public function isOk() {
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

    public function prepare($sql) {
      if ($this->connection) {
        $this->error = null;
        $stmt = mysqli_prepare($this->connection, $sql);
        return $stmt;
      } else {
        return false;
      }
    }
}
