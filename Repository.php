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
      $sql = "SELECT l.id, l.name, descr, price, img_url, dt_add, dt_expired, c.name as cat_name, cat_id" . 
      " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE dt_expired > NOW() ORDER BY dt_add DESC";
      $result = $this->query($sql);
      if ($this->isOk()) {
          $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
      }
    }
    return $lots;
  }

  public function addNewLot($lot) {
    if ($this->isOk()) {
      $sql = 'INSERT INTO lots SET dt_add = NOW(), name = $lot["name"],' . 
      ' descr = $lot["descr"], img_url = $lot["img_url"], dt_expired = $lot["date_expired"],' . 
      ' price = $lot["price"], bet_step = $lot["bet_step"], cat_id = $lot["cat_id"], author_id = $lot["author_id"]';
      $this->query($sql);
    }
    return isOk();
  }
  
  public function getLot($lotId) {
    $lot = array();
    if ($this->isOk()) {
      $sql = "SELECT l.id, l.name, descr, price, img_url, dt_add, dt_expired, bet_step, c.name as cat_name, cat_id" . 
      " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE l.id = $lotId AND dt_expired > NOW()";
      $result = $this->query($sql);
      if ($this->isOk()) {
          $lot = mysqli_fetch_assoc($result);
          return $lot;
      } 
    }
    $this->setError('Lot ' . $id . ' not found');
    return false;
  }

  public function getCatId($catName) {
    if ($this->isOk()) {
      $sql = "SELECT id, name FROM cats WHERE name = " . "'" . $catName . "'";
      $result = $this->query($sql);
      if ($this->isOk()) {
        $cat = mysqli_fetch_assoc($result);
        return $cat['id'];
      } 
    }
    $this->setError('Category ' . $catName . ' not found');
    return false;
  }  

  public function getMaxBet($lotId) {
    $bet = 0;
    $sql = "SELECT price, lot_id FROM bets WHERE lot_id = $lotId ORDER BY price DESC LIMIT 1";
    $result = $this->query($sql);
    if ($this->isOk()) {
      $row = mysqli_fetch_assoc($result);
      if (isset($row)) {
        $bet = $row['price'];
      }
    }
    return $bet;
  }
}