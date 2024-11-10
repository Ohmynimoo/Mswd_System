<?php
session_start();
include 'config.php';

// Check if the user is logged in by verifying the session variable
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");  // Redirect if not logged in
    exit();
}

// Define $userId based on the logged-in user's session
$userId = $_SESSION['userid'];

// Fetch user information to display
$user_stmt = $conn->prepare("SELECT first_name, middle_name, last_name, mobile, birthday, address, birthplace, gender FROM users WHERE id = ?");
$user_stmt->bind_param("i", $userId);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $fullname = $user['first_name'] . ' ' . ($user['middle_name'] ? $user['middle_name'] . ' ' : '') . $user['last_name'];
} else {
    $fullname = "User not found";
}

$user_stmt->close();

// Define the date cutoff (4 months before today)
$date_cutoff = date('Y-m-d', strtotime('-4 months'));

// Check if the user has uploaded files within the last 4 months
$upload_check_stmt = $conn->prepare("SELECT COUNT(*) AS recent_uploads FROM clients WHERE id = ? AND upload_date >= ?");
$upload_check_stmt->bind_param("is", $userId, $date_cutoff);
$upload_check_stmt->execute();
$upload_check_result = $upload_check_stmt->get_result();
$recent_upload = $upload_check_result->fetch_assoc()['recent_uploads'];

// If a recent upload is found, prevent further uploads and show a message
if ($recent_upload > 0) {
    echo "<div class='alert alert-warning'>You cannot upload a file because you already uploaded within the last 4 months.</div>";
} else {
    // Handle the POST request when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $middle_name = $_POST['middle_name'];
        $mobile = $_POST['mobile'];
        $birthday = $_POST['birthday'];
        $address = $_POST['address'];
        $birthplace = $_POST['birthplace'];
        $birthday_formatted = !empty($birthday) ? date('Y-m-d', strtotime($birthday)) : NULL;

        // Prepare and bind the SQL query for insertion
        $insert_stmt = $conn->prepare("INSERT INTO clients (first_name, last_name, middle_name, mobile, birthday, address, birthplace, upload_date)
                                       VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $insert_stmt->bind_param("sssssss", $first_name, $last_name, $middle_name, $mobile, $birthday_formatted, $address, $birthplace);

        // Execute the query and check for errors
        if ($insert_stmt->execute()) {
            echo "Data inserted successfully!";
        } else {
            echo "Error inserting data: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upload Files</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Main header (navbar) -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
  </nav>
  <!-- Main sidebar -->
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
              <a href="#" class="d-block"><?php echo htmlspecialchars($fullname); ?></a>
          </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="./client.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./personal_information.php" class="nav-link active">
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
            <a class="nav-link" href="#" role="button" data-widget="pushmenu">
              <i class="nav-icon far fa-bell"></i>
              <p>
                Notifications
                <span class="right badge badge-warning" id="notification-count">0</span>
              </p>
            </a>
            <ul class="nav nav-treeview" id="notification-menu">
              <li class="nav-item">
                <a class="nav-link">No Notifications</a>
              </li>
            </ul>
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
  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Upload Files</h1>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8 offset-md-2">
            <div class="card">
              <div class="card-body">
                <h3>Upload Files</h3>
                <!-- Include the PHP logic -->
                <?php include 'upload_logic.php'; ?>
                <?php if (isset($_SESSION['success_message'])): ?>
                  <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; ?>
                  </div>
                  <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                <!-- Display error messages -->
                <?php if (isset($_SESSION['error_message'])): ?>
                  <div class="alert alert-danger">
                    <?php foreach ($_SESSION['error_message'] as $error): ?>
                      <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                  </div>
                  <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
                <!-- Form for file upload -->
                <form id="uploadForm" action="" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name']); ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($user['birthday']); ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="birthplace">Birthplace</label>
                    <input type="text" class="form-control" id="birthplace" name="birthplace" value="<?php echo htmlspecialchars($user['birthplace']); ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="category">Category</label>
                      <input type="text" class="form-control" id="category" name="category" value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : (isset($_SESSION['category']) ? htmlspecialchars($_SESSION['category']) : ''); ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="files">Choose files</label>
                    <input type="file" class="form-control-file" id="files" name="files[]" multiple>
                  </div>  
                  <button type="submit" class="btn btn-success">Upload</button>
                </form>
              </div>
            </div>
          </div>
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const category = params.get('category');
    if (category) {
      document.getElementById('category').value = category;
    }
  });
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
                        notificationHtml += '<div class="notification-message ' + (notification.is_read === 0 ? 'unread' : '') + '">Comment in your request</div>';
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
            }.bind(this) // Bind the context to the current element
        });
    });
    // Fetch notifications periodically, e.g., every 30 seconds
    setInterval(fetchNotifications, 30000);
});
</script>
</body>
</html>