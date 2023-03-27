<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('location: login');
  exit;
}

require_once 'assets/dbhandler.php';
require_once 'assets/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body style="
  background: url('assets/bg4.png');
  background-size: cover;
  background-position: center center;
  background-attachment: fixed;">
  
  <nav class="navbar fixed-top navbarScroll shadow navbar-expand-lg" style="background: #457b9d;">
    <div class="container">
      <a href="dashboard" class="navbar-brand text-dark"><img src="assets/logo1.png" height="55"></a>
      <form autocomplete="off" action="search">
        <div class="input-group">
          <div class="form-outline">
            <input autofocus type="search" id="form1" class="shadow form-control" placeholder="Search Barcode" name="barcode">
          </div>
          <button type="submit" class="shadow btn btn-primary">
            <img class="icons" id="search-svg" src="assets/search.svg" width="20" height="20" alt="Search">
          </button>
        </div>
      </form>

  <?php
  $productCheck = "SELECT *  FROM `client_website`.`products`";
  $productSQL = mysqli_query($connection, $productCheck);

  if ($_SESSION['privilege'] == 'admin') {
    echo '<a class="nav-link" href="add-product"><button class="btn rounded-pill shadow btn-primary">Add Product</button></a><a class="nav-link" href="manage-accounts"><button class="btn rounded-pill shadow btn-primary">Manage Accounts</button></a><a class="nav-link" href="logs"><button id="logs" class="btn rounded-pill shadow btn-primary" name="logs">Logs</button></a><a class="nav-link" href="assets/logout"><button id="logout" class="btn btn-danger rounded-pill shadow" name="logout">Logout</button></a></div></nav>';

    // echo '<table id="product-table" class="product-table"><tr><th>Quantity</th><th>Product Name</th><th>Stock Date</th><th>Price</th><th>Total</th><th></th></tr>';

    echo '<section id="dashboard" class="intro"><div class="mask d-flex align-items-center h-100"><div class="container mt-3"><div class="row justify-content-center"><div class="mt-5 col-13"><div class="mt-5 border border-dark table-responsive table-striped shadow-lg bg-white" style="border-radius: 1rem;"><div class="card-body text-center" style="border-radius: 1rem;"><table class="table mb-0"><thead><tr><th scope="col">Barcode</th><th scope="col">Quantity</th><th scope="col">Product Name</th><th scope="col">Stock Date</th><th scope="col">Price</th><th scope="col">Price (w/ Retail Disc.)</th><th scope="col">Price (w/ Wholesale Disc.)</th><th scope="col">Total</th><th scope="col">Total (Retail)</th><th scope="col">Total (Wholesale)</th><th scope="col"></th></tr></thead><tbody>';

    while ($row = mysqli_fetch_assoc($productSQL)) {
      $retailDiscount = 100 - $row['retail_discount'];
      $wholesaleDiscount = 100 - $row['wholesale_discount'];
      $discountedRetailPrice = $row['price'] * ($retailDiscount / 100);
      $discountedWholesalePrice = $row['price'] * ($wholesaleDiscount / 100);

      $productTotal = $row['quantity'] * $row['price'];
      $retailTotal = $row['quantity'] * $discountedRetailPrice;
      $wholesaleTotal = $row['quantity'] * $discountedWholesalePrice;

      $quantityFormat = number_format($row['quantity']);
      $priceFormat = count_format_dec($row['price']);
      $retailPriceFormat = count_format_dec($discountedRetailPrice);
      $wholesalePriceFormat = count_format_dec($discountedWholesalePrice);
      $totalFormat = count_format_dec($productTotal);
      $retailTotalFormat = count_format_dec($retailTotal);
      $wholesaleTotalFormat = count_format_dec($wholesaleTotal);

      $stockDate = substr($row['stock_date'], 0, 10);
      $productId = $row['id'];

      echo '<tr><td>' . $row['barcode'] . '</td><td>' . $quantityFormat . '</td><td>' . $row['name'] . '</td><td>' . $stockDate . '</td><td>₱' . $priceFormat . '</td><td>₱' . $retailPriceFormat . '</td><td>₱' . $wholesalePriceFormat . '</td><td>₱' . $totalFormat . '</td><td>₱' . $retailTotalFormat . '</td><td>₱' . $wholesaleTotalFormat . '</td><td class="functions"><a href="edit?id=' . $row['id'] .'" title="Edit"><img class="icons" id="edit-svg" src="assets/edit.svg" width="25" height="25" alt="Edit"></a><a href="deduct?id=' . $row['id'] .'" title="Deduct Quantity"><img class="icons" id="deduct-svg" src="assets/deduct.svg" width="28" height="28" alt="Deduct Quantity"></a><a onClick="return confirm(\'Proceed to Delete?\');" href="delete?id=' . $productId . '" title="Delete"><img class="icons" id="delete-svg" src="assets/delete.svg" width="28" height="28" alt="Delete"></a></td></tr>';
    }
  } else if ($_SESSION['privilege'] == 'user') { // FOR THE USER SIDE ! ! !
    echo '<a class="nav-link" href="add-product"><button class="btn btn-primary">Add Product</button></a><a class="nav-link" href="assets/logout"><button id="logout" class="btn btn-danger" name="logout">Logout</button></a></div></nav>';

    // echo '<table id="product-table" class="product-table"><tr><th>Quantity</th><th>Product Name</th><th>Stock Date</th></tr>';

    echo '<section><section class="intro"><div class="mask d-flex align-items-center h-100"><div class="container mt-5 p-3"><div class="row justify-content-center"><div class="mt-5 col-12"><div class="table-responsive table-striped shadow-lg bg-white" style="border-radius: 1rem;"><div class="card-body text-center" style="border-radius: 1rem;"><table class="table mb-0"><thead><tr><th scope="col">Barcode</th><th scope="col">Quantity</th><th scope="col">Product Name</th><th scope="col">Stock Date</th><th scope="col"></th></tr></thead><tbody>';

    while ($row = mysqli_fetch_assoc($productSQL)) {
      $quantityFormat = number_format($row['quantity']);

      $stockDate = substr($row['stock_date'], 0, 10);
      $productId = $row['id'];

      echo '<tr><td>' . $row['barcode'] . '</td><td>' . $quantityFormat . '</td><td>' . $row['name'] . '</td><td>' . $stockDate . '</td><td class="functions"><a href="deduct?id=' . $row['id'] .'" title="Deduct Quantity"><img class="icons" id="deduct-svg" src="assets/deduct.svg" width="25" height="25" alt="Deduct Quantity"></a></td></tr>';
    }
  }

  echo '</tbody></table></div></div></div></div></div></div></section>';
  ?>
  <!-- <script src="assets/main.js"></script> -->
</body>
</html>