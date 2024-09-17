  <?php
  include 'config.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Retrieve form data and sanitize
      $fullname = htmlspecialchars(trim($_POST['fullname']));
      $mobile = htmlspecialchars(trim($_POST['mobile']));
      $birthday = htmlspecialchars(trim($_POST['birthday']));
      $address = htmlspecialchars(trim($_POST['address']));
      $password = htmlspecialchars(trim($_POST['password']));
      $repassword = htmlspecialchars(trim($_POST['repassword']));

      // Validate fields
      if (empty($fullname) || empty($mobile) || empty($birthday) || empty($address) || empty($password) || empty($repassword)) {
          $error = "All fields are required.";
      } elseif ($password !== $repassword) {
          $error = "Passwords do not match.";
      } else {
          // Check if the mobile number already exists
          $stmt = $conn->prepare("SELECT id FROM users WHERE mobile = ?");
          $stmt->bind_param("s", $mobile);
          $stmt->execute();
          $stmt->store_result();

          if ($stmt->num_rows > 0) {
              $error = "Mobile number already registered.";
          } else {
              // Hash the password
              $hashed_password = password_hash($password, PASSWORD_DEFAULT);

              // Prepare SQL statement to insert data
              $stmt = $conn->prepare("INSERT INTO users (fullname, mobile, birthday, address, password) VALUES (?, ?, ?, ?, ?)");
              $stmt->bind_param("sssss", $fullname, $mobile, $birthday, $address, $hashed_password);

              // Execute the statement
              if ($stmt->execute()) {
                  $success = "New record created successfully";
                  // Save user information in session
                  $_SESSION['user'] = [
                      'fullname' => $fullname,
                      'mobile' => $mobile,
                      'birthday' => $birthday,
                      'address' => $address
                  ];
              } else {
                  $error = "Error: " . $stmt->error;
              }

              // Close the statement
              $stmt->close();
          }
      }
  }

  // Close connection
  $conn->close();
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Page</title>
    <!-- Google Font: Source Sans Pro -->
    <!-- If you want to load fonts locally, download and include them here -->
    <link rel="stylesheet" href="path/to/local/fonts/source-sans-pro.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">

    <!-- iCheck Bootstrap -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <!-- AdminLTE Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
  </head>
  <body class="hold-transition register-page">
  <div class="register-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <div class="row">
          <div class="col-6">
            <img src="dist/img/mswdLogo.png" alt="MSWD Logo" class="img-fluid" width="150" height="100">
          </div>
          <div class="col-6">
            <img src="dist/img/bulanLogo.png" alt="Bulan Logo" class="img-fluid" width="200" height="100">
          </div>
        </div>
        <a class="h3">Welcome to <b>MSWD</b> Assistance Management System</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Register for your request</p>

        <?php if (isset($error)): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="register.php" method="post">
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Full name" name="fullname" value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Mobile number" name="mobile" maxlength="11" pattern="\d{11}" value="<?php echo isset($mobile) ? htmlspecialchars($mobile) : ''; ?>" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-phone"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="date" class="form-control" placeholder="Birthday" name="birthday" value="<?php echo isset($birthday) ? htmlspecialchars($birthday) : ''; ?>" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-calendar"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Address" name="address" value="<?php echo isset($address) ? htmlspecialchars($address) : ''; ?>" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-map-marker-alt"></span>
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
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Retype password" name="repassword" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
                <label for="agreeTerms">
                  I agree to the <a href="#" data-toggle="modal" data-target="#termsModal">terms and conditions</a>
                </label>
              </div>
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>
          </div>
        </form>

        <a href="login.php" class="text-center">I'm already registered</a>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h5>Why We Collect Your Personal Information</h5>
          <p>We collect your personal information to provide you with the best possible service and to ensure the security and integrity of our system. Your information is used for the following purposes:</p>
          <ul>
            <li><strong>Verification:</strong> To verify your identity and ensure that the person registering is who they claim to be.</li>
            <li><strong>Communication:</strong> To contact you regarding your requests and to provide updates on the status of your assistance.</li>
            <li><strong>Record Keeping:</strong> To maintain accurate records of assistance provided and to ensure compliance with legal and regulatory requirements.</li>
            <li><strong>Service Improvement:</strong> To understand your needs and improve our services based on your feedback and usage patterns.</li>
          </ul>
          <h5>Security of Your Information</h5>
          <p>We are committed to protecting your personal information. We use a variety of security measures to ensure that your data is kept safe from unauthorized access and disclosure.</p>
          <h5>Your Consent</h5>
          <p>By registering on our platform, you consent to the collection, use, and storage of your personal information as described in these terms and conditions.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
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
