<?php
// error_reporting(0);
// Login to phpmyadmin
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

$loginConn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
//Creates database if it doesn't exist
$createDB = "CREATE DATABASE IF NOT EXISTS `client_website`";
$dbQuery = mysqli_query($loginConn, $createDB);
//Connects to database
define('DB_NAME', 'client_website');
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($connection === false) {
  die('ERROR: Could not connect. ' . mysqli_connect_error());
}

$createAccTable = "CREATE TABLE IF NOT EXISTS `client_website`.`accounts` (`id` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR(255) NOT NULL , `password` VARCHAR(255) NOT NULL , `privilege` VARCHAR(255) NOT NULL , `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `date_updated` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`), UNIQUE (`username`)) ENGINE = InnoDB;";
$accTableQuery = mysqli_query($connection, $createAccTable);

$createProductTable = "CREATE TABLE IF NOT EXISTS `client_website`.`products` (`id` INT NOT NULL AUTO_INCREMENT , `barcode` VARCHAR(255) NOT NULL , `quantity` INT NOT NULL , `name` VARCHAR(255) NOT NULL , `stock_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_on` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `price` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
$productTableQuery = mysqli_query($connection, $createProductTable);
?>