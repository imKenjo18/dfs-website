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

$search = trim($_GET['date']);

if (empty($search)) {
  header('location: logs');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Search Logs on <?php echo $search; ?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"> -->
  <link rel="stylesheet" href="assets/style.css">
</head>
<body style="background-color: #e3d5ca;">
  <nav class="navbar fixed-top navbarScroll shadow-sm" style="background-color: #e3d5ca;">
    <div class="container">
      <a href="dashboard" class="navbar-brand"><h2>Dashboard</h2></a>
      <form action="search">
        <div class="input-group">
          <div class="form-outline">
            <input type="search" id="form1" class="form-control" placeholder="Search Barcode" name="barcode">
          </div>
          <button type="submit" class="btn btn-primary">
            <img class="icons" id="search-svg" src="assets/search.svg" width="20" height="20" alt="Search"></img>
          </button>
        </div>
      </form>
      <a class="nav-link" href="add-product"><button class="btn btn-success">Add Product</button></a>
      <a class="nav-link" href="manage-accounts"><button class="btn btn-info">Manage Accounts</button></a>
      <a class="nav-link" href="logs"><button id="logs" class="btn btn-warning" name="logs">Logs</button></a>
      <a class="nav-link" href="assets/logout"><button id="logout" class="btn btn-danger" name="logout">Logout</button></a>
    </div>
  </nav>
  <section id="dashboard" class="intro">
    <div class="mask d-flex align-items-center h-100">
      <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="table-responsive shadow-lg bg-white" style="border-radius: 1rem;">
              <div class="card-body" style="border-radius: 1rem;">
                <form action="log-search">
                  <table class="table mb-0">
                    <thead>
                      <tr>
                        <th scope="col" class="text-center">
                          Logs on <input type="date" name="date" min="2000-01-01" value="<?php echo $search; ?>"> <button type="submit" class="btn btn-dark btn-sm">OK</button>
                        </th>
                      </tr>
                    </thead>
                  <tbody>
            
                  <?php
                  $logCheck = "SELECT * FROM `logs` WHERE `timestamp` LIKE '%$search%' ORDER BY `logs`.`id` DESC";
                  $logSQL = mysqli_query($connection, $logCheck);

                  while ($row = mysqli_fetch_assoc($logSQL)) {
                    $logId = $row['id'];
                    $log = $row['timestamp'];
                    echo '<tr><td>[' . $row['timestamp'] . ']&nbsp&nbsp' . '<b>' . strtoupper($row['editor']) . '</b> ' .  $row['message'] . '</td></tr>';
                  }

                  echo '</tbody></table></form></div></div></div></div></div></div></section>';
                  ?>

  <script src="main.js"></script>
</body>
</html>
