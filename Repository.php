<?php
require_once('Database.php');

class Repository extends Database {

  public function getAllCategories() {
    $cats = array();
    if ($this->isOk()) {
      $sql = "SELECT name, code FROM cats";
      $result = $this->query($sql);
      if ($this->isOk()) {
          $cats = mysqli_fetch_all($result, MYSQLI_ASSOC);
      }
    }
    return $cats;
  }

  public function getAllLots() {
    $lots = array();
    if ($this->isOk()) {
      $sql = "SELECT l.name, descr, price, img_url, dt_add, dt_expired, c.name as cat_name, cat_id" . 
      " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE dt_expired > NOW() ORDER BY dt_add DESC";
      $result = $this->query($sql);
      if ($this->isOk()) {
          $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
      }
    }
    return $lots;
  }

  public function addNewLot($sql) {
    $this->query($sql);
  }
}