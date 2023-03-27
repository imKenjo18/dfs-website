<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('location: login');
  exit;
}

// if ($_SESSION['privilege'] != 'admin') {
//   header('location: dashboard');
//   exit;
// }

require_once 'assets/dbhandler.php';
require_once 'assets/functions.php';

$barcode_err = $quantity_err = $product_name_err = $price_err = $retail_discount_err = $wholesale_discount_err = $add_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['back'])) {
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
  
  if (empty($barcode_err) && empty($quantity_err) && empty($product_name_err) && empty($price_err) && empty($retail_discount_err) && empty($wholesale_discount_err)) {
    $quantity = trim($_POST['quantity']);
    $stock_date = trim($_POST['stock_date']);
    $price = str_to_float(trim($_POST['price']));
  
    if ($stock_date != date('Y-m-d')) {
      $addProductSQL = "INSERT INTO `products` (`barcode`, `quantity`, `name`, `stock_date`, `price`, `retail_discount`, `wholesale_discount`) VALUES ('$barcode', '$quantity', '$product_name', '$stock_date', '$price', '$retail_discount', '$wholesale_discount')";
    } else {
      $addProductSQL = "INSERT INTO `products` (`barcode`, `quantity`, `name`, `stock_date`, `price`, `retail_discount`, `wholesale_discount`) VALUES ('$barcode', '$quantity', '$product_name', CURRENT_TIMESTAMP, '$price', '$retail_discount', '$wholesale_discount')";
    }

    $addProductQuery = mysqli_query($connection, $addProductSQL);
    $add_success = 'Successfully added.';

    $addLogSQL = "INSERT INTO `logs` (editor, message) VALUES ('" . $_SESSION['privilege'] . "', '<span class=" . "text-success" . "><b>added</b></span> a product. (<b>Barcode:</b> $barcode | <b>Name:</b> $product_name | <b>Quantity:</b> $quantity | <b>Price:</b> $price | <b>Retail Discount:</b> $retail_discount% | <b>Wholesale Discount:</b> $wholesale_discount% | <b>Stock Date:</b> $stock_date)')";
    $addLogQuery = mysqli_query($connection, $addLogSQL);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Product</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <!-- <link rel="stylesheet" href="assets/style.css"> -->
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
            <!-- <h2 class="text-center">Add Product</h2><br> -->
            <form autocomplete="off" method="POST">
              <div class="form-group">
                <label><b>Barcode</b></label>
                <input autofocus id="barcode" class="form-control shadow-sm rounded-pill border border-dark <?php echo (!empty($barcode_err)) ? 'is-invalid' : ''; ?>" name="barcode" value="<?php
                if (empty($add_success)) {
                  if (!empty($barcode)) {
                    echo $barcode;
                  } 
                }
                ?>">
                <span class="invalid-feedback"><?php echo $barcode_err; ?></span>
              </div>

              <div class="form-group">
                <label><b>Quantity</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark" type="number" name="quantity" value="<?php
                if (empty($add_success)) {
                  if (!empty($quantity)) {
                    echo $quantity;
                  } 
                }
                ?>">
              </div>

              <div class="form-group">
                <label><b>Product Name</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark <?php echo (!empty($product_name_err)) ? 'is-invalid' : ''; ?>" name="product_name" value="<?php
                if (empty($add_success)) {
                  if (!empty($product_name)) {
                    echo $product_name;
                  } 
                }
                ?>">
                <span class="invalid-feedback"><?php echo $product_name_err; ?></span>
              </div>

              <div class="form-group">
                <label><b>Stock Date</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark" type="date" name="stock_date" value="<?php echo date('Y-m-d'); ?>" min="2000-01-01">
              </div>

              <div class="form-group">
                <label><b>Price</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark" name="price" value="<?php
                if (empty($add_success)) {
                  if (!empty($price)) {
                    echo $price;
                  } 
                }
                ?>">
              </div>

              <div class="form-group">
                <label><b>Retail Discount (%)</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark <?php echo (!empty($retail_discount_err)) ? 'is-invalid' : ''; ?>" name="retail_discount" value="<?php
                if (empty($add_success)) {
                  if (!empty($retail_discount)) {
                    echo $retail_discount;
                  } 
                }
                ?>">
                <span class="invalid-feedback"><?php echo $retail_discount_err; ?></span>
              </div>

              <div class="form-group">
                <label><b>Wholesale Discount (%)</b></label>
                <input class="form-control shadow-sm rounded-pill border border-dark <?php echo (!empty($wholesale_discount_err)) ? 'is-invalid' : ''; ?>" name="wholesale_discount" value="<?php
                if (empty($add_success)) {
                  if (!empty($wholesale_discount)) {
                    echo $wholesale_discount;
                  } 
                }
                ?>">
                <span class="invalid-feedback"><?php echo $wholesale_discount_err; ?></span>
                <?php
                if (!empty($add_success)) {
                  echo '<br><span class="alert alert-success btn-block text-center">' . $add_success . '</span>';
                }
                ?>
              </div>
              <input class="mt-4 btn btn-primary btn-block" type="submit" value="ADD PRODUCT">

              <button class="mt-2 btn btn-secondary btn-block" name="back">DASHBOARD</button>
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