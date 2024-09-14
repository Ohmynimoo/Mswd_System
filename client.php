<?php
session_start();
// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}
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
// Fetch user information
$userId = $_SESSION['userid'];
$sql = "SELECT fullname, mobile, birthday, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
        .card-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        /* Ensure the dropdown is correctly positioned below the bell icon */
        .nav-item.dropdown .dropdown-menu {
            top: 100%;  /* Position dropdown below the bell icon */
            left: auto; /* Ensure it aligns with the icon */
            right: 0;   /* Aligns the dropdown menu with the right edge of the parent */
            margin-top: 0.25rem; /* Add some space between the bell icon and dropdown */
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                    <i class="fas fa-th-large"></i>
                </a>
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
                <a href="#" class="d-block"><?php echo htmlspecialchars($user['fullname']); ?></a>
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

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Assistance to Individuals in Crisis Situation</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <img src="dist/img/medAss.png" alt="Medical Assistance" class="card-img-top card-img">
                            <div class="card-body">
                                <h3>Medical Assistance</h3>
                                <p class="card-text">Provide the following partial requirements:</p>
                                <ul>
                                    <li>Valid Id</li>
                                    <li>Barangay Indigency</li>
                                </ul>
                                <button class="btn btn-success" onclick="redirectToUpload('Medical Assistance')">Upload</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="dist/img/burial1Ass.png" alt="Burial Assistance" class="card-img-top card-img">
                            <div class="card-body">
                                <h3>Burial Assistance</h3>
                                <p class="card-text">Provide the following partial requirements:</p>
                                <ul>
                                    <li>Valid Id</li>
                                    <li>Barangay Indigency</li>
                                </ul>
                                <button class="btn btn-success" onclick="redirectToUpload('Burial Assistance')">Upload</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="dist/img/transpoAss.png" alt="Transportation Assistance" class="card-img-top card-img">
                            <div class="card-body">
                                <h3>Transportation Assistance</h3>
                                <p class="card-text">Provide the following partial requirements:</p>
                                <ul>
                                    <li>Valid Id</li>
                                    <li>Barangay Indigency</li>
                                </ul>
                                <button class="btn btn-success" onclick="redirectToUpload('Transportation Assistance')">Upload</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="dist/img/educAss.png" alt="Educational Assistance" class="card-img-top card-img">
                            <div class="card-body">
                                <h3>Educational Assistance</h3>
                                <p class="card-text">Provide the following partial requirements:</p>
                                <ul>
                                    <li>School Id</li>
                                    <li>Barangay Indigency</li>
                                </ul>
                                <button class="btn btn-success" onclick="redirectToUpload('Educational Assistance')">Upload</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="dist/img/livelihoodAss.png" alt="Livelihood Assistance" class="card-img-top card-img">
                            <div class="card-body">
                                <h3>Livelihood Assistance</h3>
                                <p class="card-text">Provide the following partial requirements:</p>
                                <ul>
                                    <li>Valid Id</li>
                                    <li>Barangay Indigency</li>
                                </ul>
                                <button class="btn btn-success" onclick="redirectToUpload('Livelihood Assistance')">Upload</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="dist/img/shelterAss.png" alt="Emergency Shelter Assistance" class="card-img-top card-img">
                            <div class="card-body">
                                <h3>Emergency Shelter Assistance</h3>
                                <p class="card-text">Provide the following partial requirements:</p>
                                <ul>
                                    <li>Valid Id</li>
                                    <li>Barangay Indigency</li>
                                </ul>
                                <button class="btn btn-success" onclick="redirectToUpload('Emergency Shelter Assistance')">Upload</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="dist/img/othersubAss.png" alt="Subsistence Assistance" class="card-img-top card-img">
                            <div class="card-body">
                                <h3>Subsistence Assistance</h3>
                                <p class="card-text">Provide the following partial requirements:</p>
                                <ul>
                                    <li>Valid Id</li>
                                    <li>Barangay Indigency</li>
                                </ul>
                                <button class="btn btn-success" onclick="redirectToUpload('Subsistence Assistance')">Upload</button>
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
<script src="client.js"></script>
</body>
</html>