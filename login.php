<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Uses a prepared statement to prevent SQL injection attacks.
    $stmt = $conn->prepare("SELECT id, password, is_admin FROM users WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);

    // Execute the statement
    $stmt->execute();

    // Bind the input result variables
    $stmt->bind_result($id, $hashed_password, $is_admin);

    // Fetch value
    if ($stmt->fetch()) {
        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Handle session depending on user role
            if ($is_admin) {
                session_name('admin_session');
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $id;
                $_SESSION['is_admin'] = true;
                header("Location: mswdDashboard.php");
            } else {
                session_name('client_session');
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

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MSWD Log In</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="path/to/local/fonts/source-sans-pro.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- iCheck Bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/MSWD.png" alt="image Logo" height="200" width="200">
    <h2>Loading...</h2>
  </div>
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
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
        </div>
      </form>
      <!-- Back Button to redirect to index.php -->
      <div class="row mt-2">
        <div class="col-12">
          <a href="index.php" class="btn btn-secondary btn-block">Back</a>
        </div>
      </div>
      <p class="mb-0">
        <a href="register.php" class="text-center">New client? Click here to register</a>
      </p>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
