<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('location: login');
  exit;
}

if ($_SESSION['privilege'] != 'admin') {
  header('location: dashboard');
  exit;
}

require_once 'assets/dbhandler.php';

$productId = $_GET['id'];
$productSelect = "SELECT * FROM `products` WHERE `id` = '$productId'";
$productQuery = mysqli_query($connection, $productSelect);
$productResult = mysqli_fetch_assoc($productQuery);

$barcode = $productResult['barcode'];
$product_name = $productResult['name'];
$quantity = $productResult['quantity'];
$price = $productResult['price'];
$stock_date = substr($productResult['stock_date'], 0, 10);

$addLogSQL = "INSERT INTO `logs` (editor, message) VALUES ('" . $_SESSION['privilege'] . "', '<span class=" . "text-danger" . "><b>deleted</b></span> a product. (<b>Barcode:</b> $barcode | <b>Name:</b> $product_name | <b>Quantity:</b> $quantity | <b>Price:</b> $price | <b>Stock Date:</b> $stock_date)')";
mysqli_query($connection, $addLogSQL);

$deleteSql = "DELETE FROM `products` WHERE `products`.`id` = '$productId'";
mysqli_query($connection, $deleteSql);

header("location: dashboard");
?>