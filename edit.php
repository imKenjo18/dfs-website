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

$barcode_err = $product_name_err = '';

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
  
  if (empty($barcode_err) && empty($product_name_err)) {
    $stock_date = trim($_POST['stock_date']);
    
    $sql = "UPDATE `products` SET `barcode` = ?, `quantity` = ?, `name` = ?, `stock_date` = ?, `price` = ? WHERE `id` = '$productId'";
    
    if ($stmt = mysqli_prepare($connection, $sql)) {
      mysqli_stmt_bind_param($stmt, "sisss", $param_barcode, $param_quantity, $param_product_name, $param_stock_date, $param_price);
      
      $quantity = trim($_POST['quantity']);
      $price = floatval(trim($_POST['price']));
      
      $param_barcode = $barcode;
      $param_quantity = $quantity;
      $param_product_name = $product_name;
      $param_stock_date = $stock_date;
      $param_price = $price;

      if (mysqli_stmt_execute($stmt)) {
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
  <title>Edit Product - <?php echo $productResult['name']; ?></title>
  <!-- <link rel="stylesheet" href="assets/style.css"> -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<section class="vh-100" style="background-color: #e3d5ca;">
  <div class=" container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-2-strong" style="border-radius: 1rem;">
          <div class="shadow-lg card-body p-5 text-left" style="border-radius: 1rem;">

            <form method="POST">

              <div class="form-group">
                <label>Barcode</label>
                <input id="barcode" class="form-control <?php echo (!empty($barcode_err)) ? 'is-invalid' : ''; ?>" name="barcode" value="<?php echo $productResult['barcode']; ?>">
                <span class="invalid-feedback"><?php echo $barcode_err; ?></span>
              </div>

              <div class="form-group">
                <label>Quantity</label>
                <input class="form-control" type="number" name="quantity" value="<?php echo $productResult['quantity']; ?>">
              </div>

              <div class="form-group">
                <label>Product Name</label>
                <input class="form-control <?php echo (!empty($product_name_err)) ? 'is-invalid' : ''; ?>" name="product_name" value="<?php echo $productResult['name']; ?>">
                <span class="invalid-feedback"><?php echo $product_name_err; ?></span>
              </div>

              <div class="form-group">
                <label>Stock Date</label>
                <input class="form-control" type="date" name="stock_date" value="<?php echo substr($productResult['stock_date'], 0, 10); ?>" min="2000-01-01">
              </div>

              <div class="form-group">
                <label>Price</label>
                <input class="form-control" name="price" value="<?php echo $productResult['price']; ?>">
              </div>

            <input class="mt-4 btn btn-primary btn-block" type="submit" value="CONFIRM EDIT">
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

</body>
</html>