<?php
session_start();
include 'config.php';

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

$statusMessages = [
    'Pending' => 'Your uploaded files are being validated by the MSWDO of Bulan Sorsogon. Please wait for an SMS notification or monitor your notifications and request status.',
    'Processing' => 'Your request is processing please bring the physical requirements to the MSWDO office for an interview. We will notify you once completed.',
    'Approved' => 'Congratulations! Your request has been approved by the MSWDO. Please visit the treasury office for payout.',
    'Denied' => 'Unfortunately, your request has been denied due to insufficient funds. Please visit the MSWD office for clarification.',
    'No Request Found' => 'No request found. Please submit a request to track its status here.'
];

$statusMessage = isset($statusMessages[$requestStatus]) ? $statusMessages[$requestStatus] : 'Unknown status.';

// Define CSS classes for status colors
$statusColors = [
    'Pending' => 'pending-status',
    'Processing' => 'processing-status',
    'Approved' => 'approved-status',
    'Denied' => 'denied-status',
    'No Request Found' => 'no-request-status'
];

$statusColorClass = isset($statusColors[$requestStatus]) ? $statusColors[$requestStatus] : 'unknown-status';
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
                            <!-- Status Timeline (visual progress) -->
                            <div class="status-step <?php echo ($requestStatus == 'Pending' || $requestStatus == 'Processing' || $requestStatus == 'Approved') ? 'completed' : ''; ?>">
                                <div class="status-circle <?php echo ($requestStatus == 'Pending') ? 'active pending' : ''; ?>" data-tooltip="Your request is pending approval">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="status-line <?php echo ($requestStatus == 'Pending' || $requestStatus == 'Processing' || $requestStatus == 'Approved') ? 'completed' : ''; ?>"></div>
                                <div class="status-text <?php echo ($requestStatus == 'Pending') ? 'active pending' : ''; ?>">Pending</div>
                            </div>

                            <div class="status-step <?php echo ($requestStatus == 'Processing' || $requestStatus == 'Approved') ? 'completed' : ''; ?>">
                                <div class="status-circle <?php echo ($requestStatus == 'Processing') ? 'active' : ''; ?>" data-tooltip="Your request is being processed">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <div class="status-line <?php echo ($requestStatus == 'Processing' || $requestStatus == 'Approved') ? 'completed' : ''; ?>"></div>
                                <div class="status-text <?php echo ($requestStatus == 'Processing') ? 'active' : ''; ?>">Processing</div>
                            </div>

                            <div class="status-step <?php echo ($requestStatus == 'Approved') ? 'completed' : ''; ?>">
                                <div class="status-circle <?php echo ($requestStatus == 'Approved') ? 'completed active' : ''; ?>" data-tooltip="Your request has been approved">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="status-line <?php echo ($requestStatus == 'Approved') ? 'completed' : ''; ?>"></div>
                                <div class="status-text <?php echo ($requestStatus == 'Approved') ? 'completed active' : ''; ?>">Approved</div>
                            </div>

                            <div class="status-step <?php echo ($requestStatus == 'Denied') ? 'denied' : ''; ?>">
                                <div class="status-circle <?php echo ($requestStatus == 'Denied') ? 'denied' : ''; ?>" data-tooltip="Your request has been denied">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div class="status-line <?php echo ($requestStatus == 'Denied') ? 'denied' : ''; ?>"></div>
                                <div class="status-text <?php echo ($requestStatus == 'Denied') ? 'denied' : ''; ?>">Denied</div>
                            </div>
                        </div>

                        <!-- Status Message Display -->
                        <div class="status-message <?php echo $statusColorClass; ?> mt-4">
                            <p><strong>Status: </strong><?php echo htmlspecialchars($requestStatus); ?></p>
                            <p><?php echo htmlspecialchars($statusMessage); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
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