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
require_once 'assets/functions.php';

$barcode_err = $product_name_err = $retail_discount_err = $wholesale_discount_err = '';

$productId = $_GET['id'];
$productSelect = "SELECT * FROM `products` WHERE `id` = '$productId'";
$productQuery = mysqli_query($connection, $productSelect);
$productResult = mysqli_fetch_assoc($productQuery);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['cancel'])) {
    header('location: dashboard');
  }

  if (empty(trim($_POST['barcode']))) {
    $barcode_err = 'Please input the product barcode.';
  } else {
    $barcode = trim($_POST['barcode']);
  }
  
  if (empty(trim($_POST['product_name']))) {
    $product_name_err = 'Please input the product name.';
  } else {
    $product_name = trim($_POST['product_name']);
  }

  $retail_discount = str_to_float(trim($_POST['retail_discount']));

  if ($retail_discount > 100 || $retail_discount < 0) {
    $retail_discount_err = 'Only accepting value from 0 to 100.';
  } 

  $wholesale_discount = str_to_float(trim($_POST['wholesale_discount']));

  if ($wholesale_discount > 100 || $wholesale_discount < 0) {
    $wholesale_discount_err = 'Only accepting value from 0 to 100.';
  }
  
  if (empty($barcode_err) && empty($product_name_err) && empty($retail_discount_err) && empty($wholesale_discount_err)) {
    $stock_date = trim($_POST['stock_date']);
    
    $sql = "UPDATE `products` SET `barcode` = ?, `quantity` = ?, `name` = ?, `stock_date` = ?, `price` = ?, `retail_discount` = ?, `wholesale_discount` = ? WHERE `id` = '$productId'";
    
    if ($stmt = mysqli_prepare($connection, $sql)) {
      mysqli_stmt_bind_param($stmt, "sisssss", $param_barcode, $param_quantity, $param_product_name, $param_stock_date, $param_price, $param_retail_discount, $param_wholesale_discount);
      
      $quantity = intval(str_to_float(trim($_POST['quantity'])));
      $price = str_to_float(trim($_POST['price']));
      
      $param_barcode = $barcode;
      $param_quantity = $quantity;
      $param_product_name = $product_name;
      $param_stock_date = $stock_date;
      $param_price = $price;
      $param_retail_discount = $retail_discount;
      $param_wholesale_discount = $wholesale_discount;

      if (mysqli_stmt_execute($stmt)) {
        $addLogSQL = "INSERT INTO `logs` (editor, message) VALUES ('" . $_SESSION['privilege'] . "', '<span class=" . "text-primary" . "><b>edited</b></span> a product. (<b>Barcode:</b> " . $productResult['barcode'] . " → $barcode | <b>Name:</b> " . $productResult['name'] . " → $product_name | <b>Quantity:</b> " . $productResult['quantity'] . " → $quantity | <b>Price:</b> " . $productResult['price'] . " → $price | <b>Retail Discount:</b> " . $productResult['retail_discount'] . "% → $retail_discount% | <b>Wholesale Discount:</b> " . $productResult['wholesale_discount'] . "% → $wholesale_discount% | <b>Stock Date:</b> " . substr($productResult['stock_date'], 0, 10) . " → $stock_date)')";
        $addLogQuery = mysqli_query($connection, $addLogSQL);

        header('location: dashboard');
        exit;
      } else {
        echo 'Oops! Something went wrong. Please try again later.';
      }

      mysqli_stmt_close($stmt);
    }
  }

  mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Product - <?php echo $productResult['name']; ?></title>
  <!-- <link rel="stylesheet" href="assets/style.css"> -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>
<body style="
  background: url('assets/bg4.png');
  background-size: cover;
  background-position: center center;
  background-attachment: fixed;">

<section class="vh-100">
  <div class=" container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-2-strong border border-dark" style="border-radius: 1rem;">
          <div class="shadow-lg card-body p-5 text-left" style="border-radius: 1rem;">
            <form autocomplete="off" method="POST">

              <div class="form-group">
                <label><b>Barcode</b></label>
                <input id="barcode" class="form-control shadow-sm rounded-pill border border-dark <?php echo (!empty($barcode_err)) ? 'is-invalid' : ''; ?>" name="barcode" value="<?php echo $productResult['barcode']; ?>">
                <span class="invalid-feedback"><?php echo $barcode_err; ?></span>
              </div>

              <div class="form-group">
                <label><b>Quantity</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark" name="quantity" value="<?php echo number_format($productResult['quantity']); ?>">
              </div>

              <div class="form-group">
                <label><b>Product Name</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark <?php echo (!empty($product_name_err)) ? 'is-invalid' : ''; ?>" name="product_name" value="<?php echo $productResult['name']; ?>">
                <span class="invalid-feedback"><?php echo $product_name_err; ?></span>
              </div>

              <div class="form-group">
                <label><b>Stock Date</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark" type="date" name="stock_date" value="<?php echo substr($productResult['stock_date'], 0, 10); ?>" min="2000-01-01">
              </div>

              <div class="form-group">
                <label><b>Price</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark" name="price" value="<?php echo count_format_dec($productResult['price']); ?>">
              </div>

              <div class="form-group">
                <label><b>Retail Discount (%)</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark <?php echo (!empty($retail_discount_err)) ? 'is-invalid' : ''; ?>" name="retail_discount" value="<?php echo $productResult['retail_discount']; ?>">
                <span class="invalid-feedback"><?php echo $retail_discount_err; ?></span>
              </div>

              <div class="form-group">
                <label><b>Wholesale Discount (%)</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark <?php echo (!empty($wholesale_discount_err)) ? 'is-invalid' : ''; ?>" name="wholesale_discount" value="<?php echo $productResult['wholesale_discount']; ?>">
                <span class="invalid-feedback"><?php echo $wholesale_discount_err; ?></span>
              </div>

            <input class="mt-4 btn btn-primary btn-block" type="submit" value="EDIT">
            </form>
            <form method="POST">
              <button class="mt-2 btn btn-secondary btn-block" name="cancel">CANCEL</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="assets/main.js"></script>
</body>
</html>