<?php
require 'dbconnector.php';
$pdo->query("
 CREATE TABLE IF NOT EXISTS
   `vertretungen` (
      `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
      `klasse` VARCHAR(20) NOT NULL,
      `stunde` VARCHAR(20) NOT NULL ,
      `s_lehrer` VARCHAR(10) NOT NULL,
      `lehrer` VARCHAR(10),
      `s_fach` VARCHAR(10) NOT NULL,
      `fach` VARCHAR(10),
      `s_raum` VARCHAR(10) NOT NULL,
      `raum` VARCHAR(10),
      `bem` CHAR(32),
      `verl` CHAR(32),
      `v_date` DATE DEFAULT NULL,
      `rc_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`ID`)
    )
    ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci
");
$pdo->query("
CREATE TABLE IF NOT EXISTS
  `subjects` (
    `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    `abbr` VARCHAR(20) NOT NULL UNIQUE,
    `full_name` VARCHAR(50) NOT NULL UNIQUE,
    PRIMARY KEY (`ID`)
  )
  ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
");

$pdo->query("
CREATE TABLE IF NOT EXISTS
  `teachers` (
    `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    `abbr` VARCHAR(20) NOT NULL UNIQUE,
    `gender` TINYINT(1) NOT NULL,
    `full_name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`ID`)
  )
  ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci
");

$pdo->query("
CREATE TABLE IF NOT EXISTS
  `daycom` (
    `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    `hash` VARCHAR(32),
    `com_date` DATE DEFAULT NULL,
    PRIMARY KEY (`ID`)
    )
    ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci
");
