<?php
// login.php

session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mswd_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password, is_admin FROM users WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);

    // Execute the statement
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($id, $hashed_password, $is_admin);

    // Fetch value
    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            if ($is_admin) {
                // Set a unique session name for admins
                session_name('admin_session');
                session_start();
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $id;
                $_SESSION['is_admin'] = true;
                header("Location: mswdDashboard.php");
            } else {
                // Set a unique session name for clients
                session_name('client_session');
                session_start();
                session_regenerate_id(true);
                $_SESSION['userid'] = $id;
                $_SESSION['is_admin'] = false;
                header("Location: client.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that mobile number.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in (v2)</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/icheck-bootstrap@3.0.1/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <div class="row">
        <div class="col-6">
          <img src="dist/img/bulanLogo.png" alt="Bulan Logo" class="img-fluid" width="150" height="100">
        </div>
        <div class="col-6">
          <img src="dist/img/mswdLogo.png" alt="MSWD Logo" class="img-fluid" width="200" height="100">
        </div>
      </div>
      <a class="h3 mt-6">Welcome to <b>MSWD</b> Assistance Management System</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your request</p>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>

      <form action="login.php" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Mobile number" name="mobile" maxlength="11" pattern="\d{11}" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-phone"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-0 ">
          
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
        </div>
      </form>
      <p class="mb-0">
        <a href="register.php" class="text-center">New client? Click here to register</a>
      </p>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>
</body>
</html>