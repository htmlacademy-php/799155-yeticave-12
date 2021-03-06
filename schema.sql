CREATE DATABASE YetiCave DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
USE YetiCave;

CREATE TABLE cats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(64) NOT NULL,
  code VARCHAR(64) NOT NULL
);

CREATE INDEX code_ind ON cats(code);

CREATE TABLE lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dt_add DATETIME NOT NULL,
  name VARCHAR(128) NOT NULL,
  descr TEXT NOT NULL,
  img_url VARCHAR(256) NOT NULL UNIQUE,
  price INT NOT NULL,
  dt_expired DATE NOT NULL,
  bet_step INT NOT NULL,
  author_id INT NOT NULL,
  winner_id INT DEFAULT NULL,
  cat_id INT NOT NULL
);

CREATE INDEX cat_ind ON lots(cat_id);

CREATE TABLE bets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dt_add DATETIME NOT NULL,
  price INT NOT NULL,
  user_id INT NOT NULL,
  lot_id INT NOT NULL
);

CREATE INDEX user_ind ON bets(user_id);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dt_reg DATETIME NOT NULL,
  email VARCHAR(64) NOT NULL UNIQUE,
  name VARCHAR(64) NOT NULL,
  password VARCHAR(64) NOT NULL,
  contact TEXT NOT NULL
);

CREATE INDEX email_ind ON users(email);
CREATE FULLTEXT INDEX name ON lots(name);
CREATE FULLTEXT INDEX descr ON lots(descr);
CREATE FULLTEXT INDEX name_descr ON lots(name,descr);
