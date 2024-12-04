<?php
session_start();
// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Fetch user information
$userId = $_SESSION['userid'];
$sql = "SELECT first_name, middle_name, last_name, mobile, birthday, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Combine first_name, middle_name, and last_name to create fullname
    $fullname = $user['first_name'] . ' ' . ($user['middle_name'] ? $user['middle_name'] . ' ' : '') . $user['last_name'];
} else {
    echo "No users found";
}

// Close connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Dashboard</title>
    <link rel="icon" href="dist/img/mswdLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
        .step-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .step-card h3 {
            margin-bottom: 15px;
            font-weight: bold;
        }
        .step-card img {
            height: 200px;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .step-arrow {
            text-align: center;
            margin: 20px 0;
        }
        .step-arrow i {
            font-size: 32px;
            color: #007bff;
        }
        .progress-bar {
            height: 5px;
            background-color: #007bff;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .step-card img {
                height: 150px;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
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
                        <a href="guide.php" class="nav-link active">
                            <i class="nav-icon fas fa-info-circle"></i>
                            <p>Guide</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./client.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Types of Assistance</p>
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

    <!-- Guide Page -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-12 text-center">
                        <h1 class="mb-4">Start Sending Assistance Requests</h1>
                        <div class="progress-bar"></div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container">
                <!-- Step 1 -->
                <div class="step-card">
                    <h3>Step 1: Select Your Chosen Type of Assistance</h3>
                    <img src="dist/img/select_assistance.png" alt="Step 1">
                    <p>Choose the assistance type that best fits your needs.</p>
                </div>
                <div class="step-arrow">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <!-- Step 2 -->
                <div class="step-card">
                    <h3>Step 2: Upload the Partial Requirements</h3>
                    <img src="dist/img/upload.png" alt="Step 2">
                    <p>Provide the necessary documents to start your request process.</p>
                </div>
                <div class="step-arrow">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <!-- Step 3 -->
                <div class="step-card">
                    <h3>Step 3: Check Notifications for Comments</h3>
                    <img src="dist/img/notifications.png" alt="Step 3">
                    <p>Stay updated through the notification section for comments on your uploaded files.</p>
                </div>
                <div class="step-arrow">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <!-- Step 4 -->
                <div class="step-card">
                    <h3>Step 4: Re-upload Files Based on Comments</h3>
                    <img src="dist/img/reupload.png" alt="Step 4">
                    <p>If needed, re-upload the required files as per the feedback provided by MSWDO.</p>
                </div>
                <div class="step-arrow">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <!-- Step 5 -->
                <div class="step-card">
                    <h3>Step 5: Monitor Your Request Status</h3>
                    <img src="dist/img/status.png" alt="Step 5">
                    <p>Track the progress of your assistance request from the status section.</p>
                </div>
                <div class="step-arrow">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <!-- Step 6 -->
                <div class="step-card">
                    <h3>Step 6: Receive SMS Notifications</h3>
                    <img src="dist/img/sms.jpg" alt="Step 6">
                    <p>Get real-time updates on your request status via SMS.</p>
                </div>
            </div>
        </section>
    </div>
    <footer class="main-footer text-center">
    </footer>
</div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.js"></script>
</body>
</html>
