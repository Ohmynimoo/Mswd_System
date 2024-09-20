<?php
session_start();
include 'config.php';

// Ensure the user is logged in
$userId = $_SESSION['userid'];
$notificationId = isset($_GET['notification_id']) ? intval($_GET['notification_id']) : null;

// Fetch comments and uploaded file information
$query = "
    SELECT 
        cn.id as notification_id, 
        cn.message, 
        c.comment, 
        u.client_name, 
        u.category, 
        u.filename,
        u.file_type,
        u.upload_date,
        u.file_data
    FROM 
        client_notifications cn
    LEFT JOIN 
        comments c ON cn.id = c.notification_id
    LEFT JOIN 
        uploads u ON cn.user_id = u.user_id
    WHERE 
        cn.user_id = ?";

$params = [$userId];

if ($notificationId) {
    $query .= " AND cn.id = ?";
    $params[] = $notificationId;
}

$query .= " ORDER BY cn.id DESC";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param(str_repeat("i", count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data structure to hold notifications and comments
$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notificationId = $row['notification_id'];
    if (!isset($notifications[$notificationId])) {
        $notifications[$notificationId] = [
            'message' => $row['message'],
            'comment' => $row['comment'],
            'client_name' => $row['client_name'],
            'files' => []
        ];
    }
    $notifications[$notificationId]['files'][] = [
        'filename' => $row['filename'],
        'file_type' => $row['file_type'],
        'file_data' => $row['file_data'],
        'upload_date' => $row['upload_date'],
        'category' => $row['category']
    ];
}

// Fetch user information and dynamically create fullname
$sql = "SELECT CONCAT(first_name, ' ', middle_name, ' ', last_name) AS fullname, mobile, birthday, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "No user found";
}
// Fetch the status from the database
$statusQuery = "SELECT status FROM notifications WHERE id = ?";
$stmt = $conn->prepare($statusQuery);
$stmt->bind_param("i", $notificationId);
$stmt->execute();
$statusResult = $stmt->get_result();
$statusRow = $statusResult->fetch_assoc();

// Ensure the result is not null before accessing the status
if ($statusRow) {
    $status = $statusRow['status'];
} else {
    // Set a default status or handle the error appropriately
    $status = 'Pending'; // Assuming Pending as the default status
}

// Close the statement and connection
$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Comments</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="view_comments.css">
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
                <a href="#" class="d-block"><?php echo htmlspecialchars($user['fullname']); ?></a>
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
                        <h1>Status of your Request</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="comment-box">
                            <h5>Notification Message:</h5>
                            <p><?php echo htmlspecialchars($notification['message']); ?></p>
                            <p><strong>Comment:</strong> <span class="highlight-comment"><?php echo htmlspecialchars($notification['comment']); ?></span></p>
                            
                            <h5>Uploaded Files:</h5>
                            <div class="uploaded-file-list">
                                <?php foreach ($notification['files'] as $file): ?>
                                    <div class="uploaded-file">
                                        <?php if (strpos($file['file_type'], 'image') === 0): ?>
                                            <img src="data:<?php echo htmlspecialchars($file['file_type']); ?>;base64,<?php echo base64_encode($file['file_data']); ?>" alt="<?php echo htmlspecialchars($file['filename']); ?>">
                                        <?php else: ?>
                                            <a href="download.php?file_id=<?php echo htmlspecialchars($notification['notification_id']); ?>">
                                                <i class="fas fa-file file-icon"></i> <?php echo htmlspecialchars($file['filename']); ?>
                                            </a>
                                        <?php endif; ?>
                                        <p><strong>Upload Date:</strong> <?php echo htmlspecialchars($file['upload_date']); ?></p>
                                        <p><strong>File Category:</strong> <?php echo htmlspecialchars($file['category']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No comments found.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.js"></script>

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
                        notificationHtml += '<div class="notification-message ' + (notification.is_read === 0 ? 'unread' : '') + '">New Comment In your Uploaded files</div>';
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