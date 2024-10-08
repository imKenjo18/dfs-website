<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('location: login');
  exit;
}

require_once 'assets/dbhandler.php';
require_once 'assets/functions.php';

$search = trim($_GET['barcode']);

if (empty($search)) {
  header('location: dashboard');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Search - <?php echo $search; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body style="
  background: url('assets/bg4.png');
  background-size: cover;
  background-position: center center;
  background-attachment: fixed;">

  <nav class="navbar fixed-top navbarScroll shadow navbar-expand-lg" style="background-color: #457b9d;">
    <div class="container">
    <a href="dashboard" class="navbar-brand text-dark"><img src="assets/logo1.png" height="55"></a>
      <form autocomplete="off" action="search">
        <div class="input-group">
          <div class="form-outline">
            <input autofocus type="search" id="form1" class="form-control shadow" placeholder="Search Barcode" name="barcode">
          </div>
          <button type="submit" class="btn btn-primary shadow">
            <img class="icons" id="search-svg" src="assets/search.svg" width="20" height="20" alt="Search"></img>
          </button>
        </div>
      </form>

      <?php
      $productCheck = "SELECT *  FROM `client_website`.`products` WHERE `barcode` = '$search'";
      $productSQL = mysqli_query($connection, $productCheck);

      if ($_SESSION['privilege'] == 'admin') {
        echo '<a class="nav-link" href="add-product"><button class="btn btn-primary shadow rounded-pill">Add Product</button></a><a class="nav-link" href="manage-accounts"><button class="btn btn-primary shadow rounded-pill">Manage Accounts</button></a><a class="nav-link" href="logs"><button id="logs" class="btn btn-primary shadow rounded-pill" name="logs">Logs</button></a><a class="nav-link" href="assets/logout"><button id="logout" class="btn btn-danger shadow rounded-pill" name="logout">Logout</button></a></div></nav>';

        // echo '<table id="product-table" class="product-table"><tr><th>Quantity</th><th>Product Name</th><th>Stock Date</th><th>Price</th><th>Total</th><th></th></tr>';

        echo '<section id="dashboard" class="intro"><div class="mask d-flex align-items-center h-100"><div class="container mt-3"><div class="row justify-content-center"><div class="mt-5 col-13"><div class="mt-5 border border-dark table-responsive shadow-lg bg-white" style="border-radius: 1rem;"><div class="card-body text-center" style="border-radius: 1rem;"><table class="table table-striped table-hover mb-0"><thead><tr><th scope="col">Barcode</th><th scope="col">Quantity</th><th scope="col">Product Name</th><th scope="col">Stock Date</th><th scope="col">Original Price</th><th scope="col">Price (w/ Retail Disc.)</th><th scope="col">Price (w/ Wholesale Disc.)</th><th scope="col">Original Total</th><th scope="col">Total (Retail)</th><th scope="col">Total (Wholesale)</th><th scope="col"></th></tr></thead><tbody>';

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
        echo '<a class="nav-link" href="add-product"><button class="btn btn-primary shadow rounded-pill">Add Product</button></a><a class="nav-link" href="assets/logout"><button id="logout" class="btn btn-danger shadow rounded-pill" name="logout">Logout</button></a></div></nav>';

        // echo '<table id="product-table" class="product-table"><tr><th>Quantity</th><th>Product Name</th><th>Stock Date</th></tr>';

        echo '<section></section><section class="intro"><div class="mask d-flex align-items-center h-100"><div class="container mt-5 pt-3"><div class="row justify-content-center"><div class="mt-5 col-12"><div class="table-responsive shadow-lg bg-white" style="border-radius: 1rem;"><div class="card-body text-center" style="border-radius: 1rem;"><table class="table table-striped table-xxl table-hover mb-0"><thead><tr><th scope="col">Barcode</th><th scope="col">Quantity</th><th scope="col">Product Name</th><th scope="col">Stock Date</th><th scope="col"></th></tr></thead><tbody>';

        while ($row = mysqli_fetch_assoc($productSQL)) {
          $productTotal = $row['quantity'] * $row['price'];
          
          $quantityFormat = number_format($row['quantity']);

          $stockDate = substr($row['stock_date'], 0, 10);
          $productId = $row['id'];
          echo '<tr><td>' . $row['barcode'] . '</td><td>' . $quantityFormat . '</td><td>' . $row['name'] . '</td><td>' . $stockDate . '</td><td class="functions"><a href="deduct?id=' . $row['id'] .'" title="Deduct Quantity"><img class="icons" id="deduct-svg" src="assets/deduct.svg" width="25" height="25" alt="Deduct Quantity"></img></a></td></tr>';
        }
      }

      echo '</tbody></table></div></div></div></div></div></div></section>';
      ?>
</body>
</html>