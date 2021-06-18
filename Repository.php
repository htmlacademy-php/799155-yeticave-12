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

  public function getLots($limit, $offset, $catId = 0) {
    $lots = array();
    if ($this->isOk()) {
      $sql = "SELECT l.id, l.name, descr, price, img_url, dt_add, dt_expired, c.name as cat_name, cat_id" . 
      " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE dt_expired > NOW()";
      if ($catId > 0) {
        $sql .= " AND l.cat_id = $catId";
      }
      $sql .= " ORDER BY dt_add DESC LIMIT $limit OFFSET $offset";
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
      if ($this->isOk()) {
        $result = mysqli_stmt_execute($stmt);
        return $result;
      }
    } 
    return false;
  }
  
  public function findSimilarLots($field, $whatToSearch) {
    $lots = array();
    $field = "l." . $field;
    $whatToSearch = "'" . $whatToSearch . "'";
      $sql = "SELECT l.id, l.name, descr, price, img_url, dt_add, dt_expired, c.name as cat_name, cat_id, " . 
    " (SELECT COUNT(b.id) FROM bets b WHERE b.id = l.id) AS bets_count" .
    " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE MATCH($field) AGAINST ($whatToSearch IN BOOLEAN MODE) " .
    "AND dt_expired > NOW() ORDER BY dt_add DESC";
    $result = $this->query($sql);
    if ($this->isOk()) {
      $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return $lots;
  }

  public function getLotsCount() {
    if ($this->isOk()) {
      $sql = "SELECT COUNT(*) AS count FROM lots WHERE dt_expired > NOW()";
      $result = $this->query($sql);
      if ($this->isOk()) {
        return mysqli_fetch_assoc($result)['count'];
      }
    }
    return 0;
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

  public function findUserName($name) {
    if ($this->isOk()) {
      $sql = "SELECT name FROM users WHERE name = " . "'" . $name . "'";
      $result = $this->query($sql);
      if ($result) {
        return mysqli_num_rows($result) > 0 ? true : false;
      }
    }
    return false;
  }
  
  public function getUser($email) {
    if ($this->isOk()) {
      $sql = "SELECT id, name, email FROM users WHERE email = " . "'" . $email . "'";
      $result = $this->query($sql);
      if ($this->isOk()) {
        $user = mysqli_fetch_assoc($result);
        return $user;
      } 
      $this->setError('email ' . $email . ' not found');
    }
    return false;
  }  
  
  public function registerNewUser($user) {
    if ($this->isOk()) {
      //запишем данные юзера в базу
      $passwordHash = password_hash($user['password'], PASSWORD_DEFAULT);		
      $sql = "INSERT INTO users (dt_reg, name, email, password, contact)" . 
      " VALUES (NOW(), ?, ?, ?, ?)";
      $data = [$user['name'], $user['email'], $passwordHash, $user['message']];
      $stmt = $this->prepare($sql, $data);
      if ($this->isOk()) {
        $result = mysqli_stmt_execute($stmt);
        return $result;
      }
    }
    return false;
  }

  public function getUserPwd($userId) {
    if ($this->isOk()) {
      $sql = "SELECT password, id FROM users WHERE id = $userId";
      $result = $this->query($sql);
      if ($this->isOk()) {
        $user = mysqli_fetch_assoc($result);
        return $user['password'];
      } 
      $this->setError('Пользователь id= ' . $userId . ' не найден');
    }
    return false;
  }  
}
