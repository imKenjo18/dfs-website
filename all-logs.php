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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Logs</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"> -->
  <link rel="stylesheet" href="assets/style.css">
</head>
<body style="
  background: radial-gradient(circle at 50% 100%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 100% 50%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 50% 0%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%), radial-gradient(circle at 0 50%, #ffffff80 5%, #ffffff 5% 10%, #ffffff80 10% 15%, #ffffff 15% 20%, #ffffff80 20% 25%, #ffffff 25% 30%, #ffffff80 30% 35%, #ffffff 35% 40%, transparent 40%);
  background-size: 1em 1em;
  background-color: #bee1e6;
  opacity: 1">
        
  <nav class="navbar fixed-top navbarScroll shadow navbar-expand-lg" style="background-color: #bee1e6;">
    <div class="container">
      <a href="dashboard" class="navbar-brand navbar-inverse"><h2 class="text-dark">Dashboard</h2></a>
      <form autocomplete="off" action="search">
        <div class="input-group">
          <div class="form-outline">
            <input autofocus type="search" id="form1" class="form-control" placeholder="Search Barcode" name="barcode">
          </div>
          <button type="submit" class="btn btn-info">
            <img class="icons" id="search-svg" src="assets/search.svg" width="20" height="20" alt="Search"></img>
          </button>
        </div>
      </form>
      <a class="nav-link" href="add-product"><button class="btn btn-info">Add Product</button></a>
      <a class="nav-link" href="manage-accounts"><button class="btn btn-info">Manage Accounts</button></a>
      <a class="nav-link" href="logs"><button id="logs" class="btn btn-info" name="logs">Logs</button></a>
      <a class="nav-link" href="assets/logout"><button id="logout" class="btn btn-danger" name="logout">Logout</button></a>
    </div>
  </nav><br><br>

  <div class="col">
    <section id="dashboard" class="intro">
      <div class="mask d-flex align-items-center h-100">
        <div class="container mt-5 pt-5">
          <div class="row justify-content-center">
            <div class=" col-12">
              <div class="mt-5 table-responsive table-striped shadow-lg bg-white" style="border-radius: 1rem;">
                <div class="card-body" style="border-radius: 1rem;">
                  <form autocomplete="off" action="log-search">
                    <table class="table mb-0">
                      <thead>
                        <tr>
                          <th scope="col" class="text-center">
                            Logs on <input type="date" name="date" min="2000-01-01"> 
                            <button type="submit" class="btn btn-dark btn-sm">OK</button>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      $logCheck = "SELECT * FROM `logs` ORDER BY `logs`.`id` DESC";
                      $logSQL = mysqli_query($connection, $logCheck);

                      while ($row = mysqli_fetch_assoc($logSQL)) {
                        $logId = $row['id'];
                        $log = $row['timestamp'];
                        echo '<tr><td>[' . $row['timestamp'] . ']&nbsp&nbsp' . '<b>' . strtoupper($row['editor']) . '</b> ' .  $row['message'] . '</td></tr>';
                      }
                      ?>
                      </tbody>
                    </table>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
<!-- <script src="assets/main.js"></script> -->
</body>
</html>