<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('location: login');
  exit;
}

require_once 'assets/dbhandler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body style="background: radial-gradient(circle at 50% 100%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 100% 50%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 50% 0%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 0 50%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%);
        background-size: 1em 1em;
        background-color: #bee1e6;
        opacity: 1">
  <nav class="navbar fixed-top navbarScroll shadow navbar-expand-lg" style="background: #bee1e6;">
        
    <div class="container">
      <a href="" class="navbar-brand text-dark"><h2>Dashboard</h2></a>
      <form autocomplete="off" action="search">
        <div class="input-group">
          <div class="form-outline">
            <input type="search" id="form1" class="form-control" placeholder="Search Barcode" name="barcode">
          </div>
          <button type="submit" class="btn btn-info">
            <img class="icons" id="search-svg" src="assets/search.svg" width="20" height="20" alt="Search"></img>
          </button>
        </div>
      </form>
          
  <?php
  $productCheck = "SELECT *  FROM `client_website`.`products`";
  $productSQL = mysqli_query($connection, $productCheck);

  if ($_SESSION['privilege'] == 'admin') {
    echo '<a class="nav-link" href="add-product"><button class="btn btn-info">Add Product</button></a><a class="nav-link" href="manage-accounts"><button class="btn btn-info">Manage Accounts</button></a><a class="nav-link" href="logs"><button id="logs" class="btn btn-info" name="logs">Logs</button></a><a class="nav-link" href="assets/logout"><button id="logout" class="btn btn-danger" name="logout">Logout</button></a></div></nav>';

    // echo '<table id="product-table" class="product-table"><tr><th>Quantity</th><th>Product Name</th><th>Stock Date</th><th>Price</th><th>Total</th><th></th></tr>';

    echo '<section id="dashboard" class="intro"><div class="mask d-flex align-items-center h-100"><div class="container mt-5 pt-5"><div class="row justify-content-center"><div class="mt-5 col-12"><div class="mt-5 table-responsive table-striped shadow-lg bg-white" style="border-radius: 1rem;"><div class="card-body text-center" style="border-radius: 1rem;"><table class="table mb-0"><thead><tr><th scope="col">Barcode</th><th scope="col">Quantity</th><th scope="col">Product Name</th><th scope="col">Stock Date</th><th scope="col">Price</th><th scope="col">Total</th><th scope="col"></th></tr></thead><tbody>';

    while ($row = mysqli_fetch_assoc($productSQL)) {
      $productTotal = $row['quantity'] * $row['price'];
      $stockDate = substr($row['stock_date'], 0, 10);
      $productId = $row['id'];
      echo '<tr><td>' . $row['barcode'] . '</td><td>' . $row['quantity'] . '</td><td>' . $row['name'] . '</td><td>' . $stockDate . '</td><td>₱' . $row['price'] . '</td><td>₱'. $productTotal . '</td><td class="functions"><a href="edit?id=' . $row['id'] .'" title="Edit"><img class="icons" id="edit-svg" src="assets/edit.svg" width="25" height="25" alt="Edit"></img></a><a href="deduct?id=' . $row['id'] .'" title="Deduct Quantity"><img class="icons" id="deduct-svg" src="assets/deduct.svg" width="28" height="28" alt="Deduct Quantity"></img></a><a onClick="return confirm(\'Proceed to Delete?\');" href="delete?id=' . $productId . '" title="Delete"><img class="icons" id="delete-svg" src="assets/delete.svg" width="28" height="28" alt="Delete"></img></a></td></tr>';
    }
  } else if ($_SESSION['privilege'] == 'user') { // FOR THE USER SIDE ! ! !
    echo '<a class="nav-link" href="add-product"><button class="btn btn-info">Add Product</button></a><a class="nav-link" href="assets/logout"><button id="logout" class="btn btn-danger" name="logout">Logout</button></a></div></nav>';

    // echo '<table id="product-table" class="product-table"><tr><th>Quantity</th><th>Product Name</th><th>Stock Date</th></tr>';

    echo '<section><section class="intro"><div class="mask d-flex align-items-center h-100"><div class="container mt-5 pt-5"><div class="row justify-content-center"><div class="mt-5 col-12"><div class="table-responsive table-striped shadow-lg bg-white" style="border-radius: 1rem;"><div class="card-body text-center" style="border-radius: 1rem;"><table class="table mb-0"><thead><tr><th scope="col">Barcode</th><th scope="col">Quantity</th><th scope="col">Product Name</th><th scope="col">Stock Date</th><th scope="col">Price</th><th scope="col">Total</th><th scope="col"></th></tr></thead><tbody>';

    while ($row = mysqli_fetch_assoc($productSQL)) {
      $productTotal = $row['quantity'] * $row['price'];
      $stockDate = substr($row['stock_date'], 0, 10);
      $productId = $row['id'];
      echo '<tr><td>' . $row['barcode'] . '</td><td>' . $row['quantity'] . '</td><td>' . $row['name'] . '</td><td>' . $stockDate . '</td><td>₱' . $row['price'] . '</td><td>₱'. $productTotal . '</td><td class="functions"><a href="deduct?id=' . $row['id'] .'" title="Deduct Quantity"><img class="icons" id="deduct-svg" src="assets/deduct.svg" width="25" height="25" alt="Deduct Quantity"></img></a></td></tr>';
    }
  }

  echo '</tbody></table></div></div></div></div></div></div></section>';
  ?>
  <script src="main.js"></script>
</body>
</html>