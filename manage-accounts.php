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

$accCheck = "SELECT * FROM `accounts`";
$accCheckQuery = mysqli_query($connection, $accCheck);
$accCheckResult = mysqli_num_rows($accCheckQuery);

if (isset($_POST['back'])) {
  header('location: dashboard');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Change Password</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body style="
  background: url('assets/bg4.png');
  background-size: cover;
  background-position: center center;
  background-attachment: fixed;">
        
  <section class="intro">
    <div class="mask d-flex align-items-center h-100">
      <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
          <div class="col-9">
            <div class="table-responsive shadow-lg bg-white" style="border-radius: 1rem;">
              <div class="card-body text-center border border-dark" style="border-radius: 1rem;">
                <h2>Manage Accounts</h2>
                <form method="POST">
                  <table class="table mt-4">
                    <thead>
                      <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Username</th>
                        <th scope="col"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // echo '<section class="intro"><div class="mask d-flex align-items-center h-100"><div class="container mt-5 pt-5"><div class="row justify-content-center"><div class="col-12"><div class="table-responsive shadow-lg bg-white" style="border-radius: 1rem;"><div class="card-body text-center" style="border-radius: 1rem;"><table class="table mb-0"><thead><tr><th scope="col">Quantity</th><th scope="col">Product Name</th><th scope="col">Stock Date</th><th scope="col">Price</th><th scope="col">Total</th><th scope="col"></th></tr></thead><tbody>';

                      while ($account = mysqli_fetch_assoc($accCheckQuery)) {
                        echo '<tr><td><center>' . ucfirst($account['privilege']) . '</center></td><td><center>' . $account['username'] . '<center></td><td class="functions"><a href="account-settings?username=' . $account['username'] . '" title="Settings"><img class="icons" id="settings-svg" src="assets/settings.svg" width="28" height="28" alt="Settings"></img></a></td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                  <?php
                  if ($accCheckResult < 2) {
                    echo '<button class="btn btn-primary" name="add_user" style="width: 53%;">ADD ACCOUNT</button>';

                    if (isset($_POST['add_user'])) {
                      header('location: register');
                    }
                  }
                  ?>
                  <button class="btn btn-secondary mt-2" name="back" style="width: 53%;">DASHBOARD</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>