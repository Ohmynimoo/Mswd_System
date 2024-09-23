<?php
session_start();    
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mswd_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['userid'];

// Fetch the latest request status for the user
$sql = "SELECT request_status FROM uploads WHERE user_id = ? ORDER BY id DESC LIMIT 1";  // Fetch the latest status
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Default to "No Request Found" if no request exists
$requestStatus = "No Request Found";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $requestStatus = $row['request_status'];  // e.g. Pending, Processing, Approved, Denied
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Status</title>
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="request_status.css">
</head>
<body class="hold-transition sidebar-mini">
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

    <!-- Main content -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Request Status</h1>
                    </div>
                </div>
            </div>
        </section>
        <!-- Status display -->
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Your Request Status</h3>
                    </div>
                    <div class="card-body">
                    <div class="status-container">
                        <!-- Pending Status -->
                        <div class="status-step <?php echo ($requestStatus == 'Pending' || $requestStatus == 'Processing' || $requestStatus == 'Approved') ? 'completed' : ''; ?>">
                            <div class="status-circle 
                            <?php echo ($requestStatus == 'Pending') ? 'active pending' : ''; ?>" data-tooltip="Your request is pending approval">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="status-line <?php echo ($requestStatus == 'Pending' || $requestStatus == 'Processing' || $requestStatus == 'Approved') ? 'completed' : ''; ?>"></div>
                            <div class="status-text 
                            <?php echo ($requestStatus == 'Pending') ? 'active pending' : ''; ?>">Pending</div>
                        </div>

                        <!-- Processing Status -->
                        <div class="status-step <?php echo ($requestStatus == 'Processing' || $requestStatus == 'Approved') ? 'completed' : ''; ?>">
                              <div class="status-circle 
                            <?php echo ($requestStatus == 'Processing') ? 'active' : ''; ?>" data-tooltip="Your request is being processed">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <div class="status-line <?php echo ($requestStatus == 'Processing' || $requestStatus == 'Approved') ? 'completed' : ''; ?>"></div>
                            <div class="status-text 
                            <?php echo ($requestStatus == 'Processing') ? 'active' : ''; ?>">Processing</div>
                        </div>

                        <!-- Approved Status -->
                        <div class="status-step <?php echo ($requestStatus == 'Approved') ? 'completed' : ''; ?>">
                            <div class="status-circle <?php echo ($requestStatus == 'Approved') ? 'completed active' : ''; ?>" data-tooltip="Your request has been approved">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="status-line <?php echo ($requestStatus == 'Approved') ? 'completed' : ''; ?>"></div>
                            <div class="status-text <?php echo ($requestStatus == 'Approved') ? 'completed active' : ''; ?>">Approved</div>
                        </div>

                        <!-- Denied Status -->
                        <div class="status-step <?php echo ($requestStatus == 'Denied') ? 'denied' : ''; ?>">
                            <div class="status-circle <?php echo ($requestStatus == 'Denied') ? 'denied' : ''; ?>" data-tooltip="Your request has been denied">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="status-line <?php echo ($requestStatus == 'Denied') ? 'denied' : ''; ?>"></div>
                            <div class="status-text <?php echo ($requestStatus == 'Denied') ? 'denied' : ''; ?>">Denied</div>
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
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
