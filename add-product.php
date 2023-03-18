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

$barcode_err = $quantity_err = $product_name_err = $price_err = $add_success = '';

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
  
  if (empty($barcode_err) && empty($quantity_err) && empty($product_name_err) && empty($price_err)) {
    $quantity = trim($_POST['quantity']);
    $stock_date = trim($_POST['stock_date']);
    $price = floatval(trim($_POST['price']));
  
    if ($stock_date != date('Y-m-d')) {
      $addProductSQL = "INSERT INTO `products` (`barcode`, `quantity`, `name`, `stock_date`, `price`) VALUES ('$barcode', '$quantity', '$product_name', '$stock_date', '$price')";
    } else {
      $addProductSQL = "INSERT INTO `products` (`barcode`, `quantity`, `name`, `stock_date`, `price`) VALUES ('$barcode', '$quantity', '$product_name', CURRENT_TIMESTAMP, '$price')";
    }

    $addProductQuery = mysqli_query($connection, $addProductSQL);
    $add_success = 'Successfully added.';

    $addLogSQL = "INSERT INTO `logs` (editor, message) VALUES ('" . $_SESSION['privilege'] . "', 'added a product. (<b>Barcode:</b> $barcode | <b>Name:</b> $product_name | <b>Quantity:</b> $quantity | <b>Price:</b> $price | <b>Stock Date:</b> $stock_date)')";
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
  <!-- <link rel="stylesheet" href="assets/style.css"> -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background: radial-gradient(circle at 50% 100%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 100% 50%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 50% 0%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 0 50%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%);
        background-size: 1em 1em;
        background-color: #bee1e6;
        opacity: 1">

<section class="vh-100">
  <div class=" container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-2-strong" style="border-radius: 1rem;">
          <div class="shadow-lg card-body p-5 text-left" style="border-radius: 1rem;">
            <!-- <h2 class="text-center">Add Product</h2><br> -->
            <form autocomplete="off" method="POST">
              <div class="form-group">
                <label>Barcode</label>
                <input id="barcode" class="form-control <?php echo (!empty($barcode_err)) ? 'is-invalid' : ''; ?>" name="barcode" value="<?php
                if (empty($add_success)) {
                  if (!empty($barcode)) {
                    echo $barcode;
                  } 
                }
                ?>">
                <span class="invalid-feedback"><?php echo $barcode_err; ?></span>
              </div>

              <div class="form-group">
                <label>Quantity</label>
                <input class="form-control" type="number" name="quantity" value="<?php
                if (empty($add_success)) {
                  if (!empty($quantity)) {
                    echo $quantity;
                  } 
                }
                ?>">
              </div>

              <div class="form-group">
                <label>Product Name</label>
                <input class="form-control <?php echo (!empty($product_name_err)) ? 'is-invalid' : ''; ?>" name="product_name" value="<?php
                if (empty($add_success)) {
                  if (!empty($product_name)) {
                    echo $product_name;
                  } 
                }
                ?>">
                <span class="invalid-feedback"><?php echo $product_name_err; ?></span>
              </div>
              
              <div class="form-group">
                <label>Stock Date</label>
                <input class="form-control" type="date" name="stock_date" value="<?php echo date('Y-m-d'); ?>" min="2000-01-01">
              </div>

              <div class="form-group">
                <label>Price</label>
                <input class="form-control" name="price" value="<?php
                if (empty($add_success)) {
                  if (!empty($price)) {
                    echo $price;
                  } 
                }
                ?>">
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