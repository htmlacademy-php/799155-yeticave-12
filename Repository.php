<?php
require_once('Database.php');

class Repository extends Database {

  public function getAllCategories() {
    $cats = array();
    if ($this->isOk()) {
      $sql = "SELECT id, name, code FROM cats";
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

  public function addNewLot($lot, $authorId) {
    if ($this->isOk()) {
      //запишем данные лота в базу
      $sql = "INSERT INTO lots (dt_add, name, descr, img_url, price, dt_expired, bet_step, cat_id, author_id)" . 
      " VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
      $data = [$lot['lot-name'], $lot['message'], 'uploads/' . $lot['new-img'], $lot['lot-rate'], $lot['lot-date'],
          $lot['lot-step'], $lot['category'], $authorId];
      $stmt = $this->prepare($sql, $data);
      if ($stmt) {
        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
          $this->setError($this->getBaseError());
        }
        return $result;
      } else {
        $this->setError($this->getBaseError());
      }
    } 
    return false;
  }
  
  public function findSimilarLot($key, $data) {
    $sql = "SELECT id, name FROM lots WHERE name LIKE " . "'" . $data[$key] . "'";
    $result = $this->query($sql);
    if ($result) {
      $lot = mysqli_fetch_assoc($result);
      return $lot;
    }
    return false;
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
    $this->setError('Lot id ' . $id . ' not found');
    return false;
  }

  public function getCat($catId) {
    if ($this->isOk()) {
      $sql = "SELECT id, name FROM cats WHERE id = $catId";
      $result = $this->query($sql);
      if ($this->isOk()) {
        $cat = mysqli_fetch_assoc($result);
        return $cat;
      } 
    }
    $this->setError('Category id ' . $catId . ' not found');
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