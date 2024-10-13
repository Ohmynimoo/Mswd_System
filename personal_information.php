<?php
session_start();

// Database connection
include 'config.php';

$userId = $_SESSION['userid'];
$updateSuccess = false;  // Flag to track if update is successful

// Update user information if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'];
    $lastName = $_POST['last_name'];
    $mobile = $_POST['mobile'];
    $birthday = $_POST['birthday'];
    $address = $_POST['address'];
    $birthplace = $_POST['birthplace'];
    $gender = $_POST['gender'];

    // Server-side validation: Check if any fields are empty
    if (empty($firstName) || empty($middleName) || empty($lastName) || empty($mobile) || empty($birthday) || empty($address) || empty($birthplace) || empty($gender)) {
        echo "Error: All fields must be filled in.";
    } elseif (!preg_match('/^[0-9]{11}$/', $mobile)) {
        echo "Error: Mobile number must be exactly 11 digits.";
    } else {
        $sqlUpdate = "UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, mobile = ?, birthday = ?, address = ?, birthplace = ?, gender = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ssssssssi", $firstName, $middleName, $lastName, $mobile, $birthday, $address, $birthplace, $gender, $userId);

        if ($stmtUpdate->execute()) {
            $updateSuccess = true;  // Set success flag to true
            
            // Use the PRG pattern to avoid form resubmission on reload
            header("Location: personal_information.php?success=1");
            exit;  // Stop further execution to ensure redirection happens
        } else {
            echo "Error updating information: " . $conn->error;
        }
        $stmtUpdate->close();
    }
}

// Fetch user information
$sql = "SELECT first_name, middle_name, last_name, mobile, birthday, address, birthplace, gender FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Construct the full name by concatenating first, middle, and last names
    $fullname = $user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name'];
} else {
    echo "No user found";
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Personal Information</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/toastr/toastr.min.css"> <!-- Add toastr CSS -->

</head>
<body class="hold-transition sidebar-mini">
    <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/MSWD.png" alt="image Logo" height="200" width="200">
    <h2>Loading...</h2>
  </div>
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="index3.html" class="brand-link">
            <img src="dist/img/mswdLogo.png" alt="MSWDO Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">MSWDO</span>
        </a>

        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <!-- Display the full name -->
                    <a href="#" class="d-block"><?php echo htmlspecialchars($fullname); ?></a>
                </div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="./client.php" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./personal_information.php" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Personal Information</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./request_status.php" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Status</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="view_comments.php" class="nav-link">
                            <i class="far fa-bell"></i>
                            <p>
                                Notifications
                                <span class="right badge badge-warning" id="notification-count">0</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>User Details</h1>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Your Personal Information will be Submitted when Uploading a File for a Request</h3>
          </div>
          <form method="post" action="" id="edit-form">
            <div class="card-body">
              <!-- First Name -->
              <strong><i class="fas fa-user mr-1"></i> First Name</strong>
              <p class="text-muted view-mode"><?php echo htmlspecialchars($user['first_name']); ?></p>
              <input type="text" class="form-control edit-mode" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required style="display:none;">
              <hr>

              <!-- Middle Name -->
              <strong><i class="fas fa-user mr-1"></i> Middle Name</strong>
              <p class="text-muted view-mode"><?php echo htmlspecialchars($user['middle_name']); ?></p>
              <input type="text" class="form-control edit-mode" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name']); ?>" required style="display:none;">
              <hr>

              <!-- Last Name -->
              <strong><i class="fas fa-user mr-1"></i> Last Name</strong>
              <p class="text-muted view-mode"><?php echo htmlspecialchars($user['last_name']); ?></p>
              <input type="text" class="form-control edit-mode" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required style="display:none;">
              <hr>

              <!-- Mobile -->
              <strong><i class="fas fa-phone mr-1"></i> Mobile</strong>
              <p class="text-muted view-mode"><?php echo htmlspecialchars($user['mobile']); ?></p>
              <input type="text" class="form-control edit-mode" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" pattern="[0-9]{11}" maxlength="11" required style="display:none;">
              <hr>

              <!-- Birthday -->
              <strong><i class="fas fa-birthday-cake mr-1"></i> Birthday</strong>
              <p class="text-muted view-mode"><?php echo htmlspecialchars($user['birthday']); ?></p>
              <input type="date" class="form-control edit-mode" name="birthday" value="<?php echo htmlspecialchars($user['birthday']); ?>" required style="display:none;">
              <hr>

              <!-- Address -->
              <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
              <p class="text-muted view-mode"><?php echo htmlspecialchars($user['address']); ?></p>
              <input type="text" class="form-control edit-mode" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required style="display:none;">
              <hr>

              <!-- Birthplace -->
              <strong><i class="fas fa-map-marker-alt mr-1"></i> Birthplace</strong>
              <p class="text-muted view-mode"><?php echo htmlspecialchars($user['birthplace']); ?></p>
              <input type="text" class="form-control edit-mode" name="birthplace" value="<?php echo htmlspecialchars($user['birthplace']); ?>" required style="display:none;">
              <hr>

              <!-- Gender Dropdown -->
              <strong><i class="fas fa-venus-mars mr-1"></i> Gender</strong>
              <p class="text-muted view-mode"><?php echo htmlspecialchars($user['gender']); ?></p>
              <select class="form-control edit-mode" name="gender" required style="display:none;">
                <option value="Male" <?php echo ($user['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($user['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($user['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
              </select>
              <hr>

              <div class="card-footer">
                <button type="button" class="btn btn-secondary" id="edit-btn">Edit</button>
                <button type="button" class="btn btn-secondary" id="cancel-btn" style="display:none;">Cancel</button>
                <button type="submit" class="btn btn-primary" id="save-btn" style="display:none;">Save Changes</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <strong>&copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.2.0
    </div>
  </footer>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.js"></script>
<script src="plugins/toastr/toastr.min.js"></script> <!-- Add toastr JS -->

<script>
  $(document).ready(function() {
    function fetchNotifications() {
        $.ajax({
            type: 'GET',
            url: 'client_notifications.php',
            dataType: 'json',
            success: function(data) {
                var notificationMenuHtml = '';

                if (Array.isArray(data) && data.length > 0) {
                    $('#notification-count').text(data.length);
                    $.each(data, function(index, notification) {
                        var notificationHtml = '<li class="nav-item dropdown-item notification-link" data-id="' + notification.id + '">';
                        notificationHtml += '<div class="notification-message ' + (notification.is_read === 0 ? 'unread' : '') + '">Comment In Your Request</div>';
                        notificationHtml += '</li>';
                        notificationMenuHtml += notificationHtml;
                    });
                } else {
                    $('#notification-count').text(0);
                    notificationMenuHtml = '<li class="nav-item dropdown-item"><a class="nav-link">No notifications found.</a></li>';
                }

                $('#notification-menu').html(notificationMenuHtml);
            },
            error: function(xhr, status, error) {
                alert('Error fetching notifications!');
            }
        });
    }

    // Fetch notifications when the page loads
    fetchNotifications();

    // Handle click on notification
    $('#notification-menu').on('click', '.notification-link', function(event) {
        event.preventDefault();
        var notificationId = $(this).data('id');
        var notificationLink = 'view_comments.php?notification_id=' + notificationId;
        
        // Mark as read
        $.ajax({
            type: 'POST',
            url: 'mark_as_read.php',
            data: { id: notificationId },
            success: function(response) {
                var currentCount = parseInt($('#notification-count').text());
                $('#notification-count').text(currentCount - 1);
                $(this).find('.notification-message').removeClass('unread'); // Remove unread class
                window.location.href = notificationLink; // Redirect to notification details
            }.bind(this)
        });
    });

    // Fetch notifications periodically, e.g., every 30 seconds
    setInterval(fetchNotifications, 30000);
});

    // JavaScript for toggling between edit and view modes
    document.getElementById('edit-btn').addEventListener('click', function() {
        document.querySelectorAll('.view-mode').forEach(function(element) {
            element.style.display = 'none';
        });
        document.querySelectorAll('.edit-mode').forEach(function(element) {
            element.style.display = 'block';
        });
        document.getElementById('edit-btn').style.display = 'none';
        document.getElementById('cancel-btn').style.display = 'inline-block';
        document.getElementById('save-btn').style.display = 'inline-block';
    });

    document.getElementById('cancel-btn').addEventListener('click', function() {
        document.querySelectorAll('.edit-mode').forEach(function(element) {
            element.style.display = 'none';
        });
        document.querySelectorAll('.view-mode').forEach(function(element) {
            element.style.display = 'block';
        });
        document.getElementById('edit-btn').style.display = 'inline-block';
        document.getElementById('cancel-btn').style.display = 'none';
        document.getElementById('save-btn').style.display = 'none';
    });

    // Form validation for mobile number and required fields
    document.getElementById('edit-form').addEventListener('submit', function(event) {
        const mobileInput = document.querySelector('input[name="mobile"]');
        const mobileValue = mobileInput.value;
        let isFormValid = true;

        // Check if any input is empty
        document.querySelectorAll('.edit-mode').forEach(function(input) {
            if (!input.value) {
                isFormValid = false;
                alert('All fields must be filled in.');
                event.preventDefault(); // Prevent form submission
                return;
            }
        });

        // Check if mobile number is exactly 11 digits
        if (!/^[0-9]{11}$/.test(mobileValue)) {
            alert('Mobile number must be exactly 11 digits.');
            event.preventDefault();  // Prevent form submission if validation fails
        }

        if (!isFormValid) {
            event.preventDefault(); // Stop form submission if validation failed
        }
    });

    // Display toast on success based on the query string
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        toastr.success("Information updated successfully!");
    }
</script>
</body>
</html>
