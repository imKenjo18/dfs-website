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
        $addLogSQL = "INSERT INTO `logs` (editor, message) VALUES ('" . $_SESSION['privilege'] . "', 'deducted the QUANTITY of (<b>Barcode:</b> " . $productResult['barcode'] . " | <b>Name:</b> " . $productResult['name'] . " | <b>Stock Date:</b> " . substr($productResult['stock_date'], 0, 10) . ") from <b>" . $productResult['quantity'] . "</b> to <b>$param_deduction</b>')";
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
  <title>Deduct Quantity - <?php echo $productResult['name']; ?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- <link rel="stylesheet" href="assets/style.css"> -->
</head>
<body style="
  background: radial-gradient(circle at 50% 100%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 100% 50%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 50% 0%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 0 50%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%);
  background-size: 1em 1em;
  background-color: #bee1e6;
  opacity: 1">

<section class="vh-100">
  <div class=" container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-2-strong" style="border-radius: 1rem;">
          <div class="shadow-lg card-body p-5 text-left" style="border-radius: 1rem;">
            <form autocomplete="off" method="POST">

              <div class="form-group">
                <label>Barcode</label>
                <input disabled class="form-control" name="barcode" value="<?php echo $productResult['barcode']; ?>">
              </div>

              <div class="form-group">
                <label>Product Name</label>
                <input disabled class="form-control" name="product_name" value="<?php echo $productResult['name']; ?>">
              </div>

              <div class="form-group">
                <label>Quantity</label>
                <input disabled class="form-control" name="quantity" value="<?php echo number_format($productResult['quantity']); ?>">
              </div>

              <div class="form-group">
                <label>Amount to Deduct</label>
                <input type="number" class="form-control <?php echo (!empty($deduction_err)) ? 'is-invalid' : ''; ?>" name="deduction">
                <span class="invalid-feedback"><?php echo $deduction_err; ?></span>
              </div>

            <input class="mt-4 btn btn-primary btn-block" type="submit" value="DEDUCT">
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