<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('location: login');
  exit;
}

require_once 'assets/dbhandler.php';

$deduction_err = '';

$productId = $_GET['id'];
$productSelect = "SELECT * FROM `products` WHERE `id` = '$productId'";
$productQuery = mysqli_query($connection, $productSelect);
$productResult = mysqli_fetch_assoc($productQuery);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['cancel'])) {
    header('location: dashboard');
  }
  
  $quantity = $productResult['quantity'];
  $deduction = trim($_POST['deduction']);

  if (empty($deduction)) {
    $deduction_err = 'Please input how much to deduct.';
  } else if ($deduction > $quantity) {
    $deduction_err = "You can't deduct more than the quantity.";
  } else if ($deduction < 1) {
    $deduction_err = 'Minimum deduction is 1.';
  }
  
  if (empty($deduction_err)) {    
    $sql = "UPDATE `products` SET `quantity` = ? WHERE `id` = '$productId'";
    
    if ($stmt = mysqli_prepare($connection, $sql)) {
      mysqli_stmt_bind_param($stmt, "i", $param_deduction);
      
      $param_deduction = $quantity - $deduction;

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
  <title>Deduct Quantity - <?php echo $productResult['name']; ?></title>
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
                <input disabled id="barcode" class="form-control" name="barcode" value="<?php echo $productResult['barcode']; ?>">
              </div>

              <div class="form-group">
                <label>Product Name</label>
                <input disabled class="form-control" name="product_name" value="<?php echo $productResult['name']; ?>">
              </div>

              <div class="form-group">
                <label>Quantity</label>
                <input disabled class="form-control" type="number" name="quantity" value="<?php echo $productResult['quantity']; ?>">
              </div>

              <div class="form-group">
                <label>Deduct How Many?</label>
                <input class="form-control <?php echo (!empty($deduction_err)) ? 'is-invalid' : ''; ?>" name="deduction">
                <span class="invalid-feedback"><?php echo $deduction_err; ?></span>
              </div>

            <input class="mt-4 btn btn-primary btn-block" type="submit" value="DEDUCT QUANTITY">
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