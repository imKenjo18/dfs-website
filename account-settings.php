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

$username = $_GET['username'];
$accCheck = "SELECT * FROM `accounts` WHERE `username` = '$username'";
$accCheckQuery = mysqli_query($connection, $accCheck);
$accCheckResult = mysqli_num_rows($accCheckQuery);

$new_password = $confirm_password = $new_password_err = $confirm_password_err = $username_err = '';

if ($accCheckResult == 1) {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['change_password'])) {
      if (empty(trim($_POST['new_password']))) {
        $new_password_err = 'Please enter the new password.';
      } else if (strlen(trim($_POST['new_password'])) < 8) {
        $new_password_err = 'Password must have at least 8 characters.';
      } else {
        $new_password = trim($_POST['new_password']);
      }
    
      if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = 'Please confirm the password.';
      } else {
        $confirm_password = trim($_POST['confirm_password']);
    
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
          $confirm_password_err = 'Password did not match.';
        }
      }
    
      if (empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "UPDATE `accounts` SET password = ? WHERE `username` = '$username'";
    
        if ($stmt = mysqli_prepare($connection, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $param_password);
    
          $param_password = password_hash($new_password, PASSWORD_DEFAULT);
    
          if (mysqli_stmt_execute($stmt)) {
            header('location: manage-accounts');
            exit;
          } else {
            echo 'Oops! Something went wrong. Please try again later.';
          }
    
          mysqli_stmt_close($stmt);
        }
      }
    } else if (isset($_POST['change_username'])) {
      if (empty(trim($_POST['new_username']))) {
        $username_err = 'Please enter a username.';
      } else if (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['new_username']))) {
        $username_err = 'Username can only contain letters, numbers, and underscores.';
      } else {
        $sql = "SELECT id FROM `accounts` WHERE `username` = ?";
  
        if ($stmt = mysqli_prepare($connection, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $param_username);
  
          $param_username = trim($_POST['new_username']);
  
          if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
  
            if (mysqli_stmt_num_rows($stmt) == 1) {
              $username_err = 'This username is already used.';
            } else {
              $new_username = trim($_POST['new_username']);
            }
          } else {
            echo 'Oops! Something went wrong. Please try again later.';
          }
  
          mysqli_stmt_close($stmt);
        }
      }
  
      if (empty($username_err)) {
        $sql = "UPDATE `accounts` SET `username` = ? WHERE `accounts`.`username` = '$username'";

        if ($stmt = mysqli_prepare($connection, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $param_username);

          $param_username = $new_username;

          if (mysqli_stmt_execute($stmt)) {
            header('location: manage-accounts');
            exit;
          } else {
            echo 'Oops! Something went wrong. Please try again later.';
          }

          mysqli_stmt_close($stmt);
        }
      }
    }
    
    mysqli_close($connection);
  }
} else {
  header('location: manage-accounts');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Change Password</title>
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
        <div class="card shadow-2-strong" style="border-radius: 1rem;">
          <div class="shadow-lg card-body p-5 text-center" style="border-radius: 1rem;">
              <h2>Change Username</h2>
              <form action="<?php echo 'account-settings?username=' . $username; ?>" method="POST">

                <div class="form-group">
                  <!-- <label>Username</label> -->
                  <input placeholder="Username" name="new_username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                  <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>

                <div class="form-group">
                  <input type="submit" class="btn btn-primary p-2 btn-block" name="change_username" value="CHANGE USERNAME">
                </div>
              </form>

            <hr style="border-color: darkgray;">

              <h2>Change Password</h2>
              <form action="<?php echo 'account-settings?username=' . $username; ?>" method="POST">

                <div class="form-group">
                  <!-- <label>New Password</label> -->
                  <input placeholder="Password" type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                  <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                </div>

                <div class="form-group">
                  <!-- <label>Confirm Password</label> -->
                  <input placeholder="Confirm Password" type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                  <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>

                <div class="form-group">
                  <input type="submit" class="btn btn-primary p-2 btn-block" name="change_password" value="CHANGE PASSWORD">
                </div>
              </form>

            <form method="POST">
              <button class="btn btn-secondary" name="back">BACK</button>
                <?php
                if (isset($_POST['back'])) {
                  header('location: manage-accounts');
                }
                ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>